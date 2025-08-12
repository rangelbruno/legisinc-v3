<?php

namespace App\Services\Template;

use App\Models\DocumentoTemplate;
use App\Models\Proposicao;
use App\Models\ProposicaoTemplateInstance;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Exception;

class TemplateInstanceService
{
    public function criarInstanciaTemplate(int $proposicaoId, int $templateId): ProposicaoTemplateInstance
    {
        $template = DocumentoTemplate::findOrFail($templateId);
        $proposicao = Proposicao::findOrFail($proposicaoId);
        
        // Verificar se já existe instância
        $instanciaExistente = ProposicaoTemplateInstance::where('proposicao_id', $proposicaoId)
            ->where('template_id', $templateId)
            ->first();
            
        if ($instanciaExistente) {
            return $instanciaExistente;
        }
        
        // 1. Criar cópia física específica para esta proposição
        $arquivoInstancePath = $this->criarCopiaProposicao($template, $proposicao);
        
        // 2. Gerar document key única para OnlyOffice
        $documentKey = $this->gerarDocumentKey($proposicaoId, $templateId);
        
        // 3. Criar registro da instância
        return ProposicaoTemplateInstance::create([
            'proposicao_id' => $proposicaoId,
            'template_id' => $templateId,
            'arquivo_instance_path' => $arquivoInstancePath,
            'document_key' => $documentKey,
            'status' => 'preparando'
        ]);
    }
    
    private function criarCopiaProposicao(DocumentoTemplate $template, Proposicao $proposicao): string
    {
        if (!$template->arquivo_modelo_path || !Storage::exists($template->arquivo_modelo_path)) {
            throw new Exception('Template não possui arquivo modelo válido');
        }
        
        // Gerar nome único para a instância
        $nomeInstancia = 'templates/instances/' . $proposicao->id . '_' . $template->id . '_' . time() . '.docx';
        $caminhoCompleto = 'public/' . $nomeInstancia;
        
        // Criar diretório se não existir
        $diretorio = dirname(storage_path('app/' . $caminhoCompleto));
        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0755, true);
        }
        
        // Copiar arquivo modelo para instância
        Storage::copy($template->arquivo_modelo_path, $caminhoCompleto);
        
        return $caminhoCompleto;
    }
    
    private function gerarDocumentKey(int $proposicaoId, int $templateId): string
    {
        return 'instance_' . $proposicaoId . '_' . $templateId . '_' . time() . '_' . uniqid();
    }
    
    public function processarVariaveisInstance(
        ProposicaoTemplateInstance $instance, 
        array $variaveisPreenchidas
    ): void {
        // 1. Mesclar variáveis do sistema + preenchidas
        $todasVariaveis = array_merge(
            $this->obterVariaveisSistema($instance->proposicao),
            $variaveisPreenchidas
        );
        
        // 2. Usar OnlyOffice Document Builder API para substituição
        $this->substituirVariaveisViaOnlyOffice($instance, $todasVariaveis);
        
        // 3. Atualizar status
        $instance->update([
            'variaveis_preenchidas' => $variaveisPreenchidas,
            'status' => 'pronto'
        ]);
    }
    
    private function obterVariaveisSistema(Proposicao $proposicao): array
    {
        $user = auth()->user();
        $now = now();
        
        return [
            'data' => $now->format('d/m/Y'),
            'data_extenso' => $now->translatedFormat('l, d \d\e F \d\e Y'),
            'mes_atual' => $now->translatedFormat('F'),
            'ano_atual' => $now->format('Y'),
            'dia_atual' => $now->format('d'),
            'hora_atual' => $now->format('H:i'),
            'nome_parlamentar' => $user ? $user->name : '',
            'cargo_parlamentar' => $user && $user->perfil ? $user->perfil->nome : '',
            'email_parlamentar' => $user ? $user->email : '',
            'numero_proposicao' => $proposicao->numero ?? '',
            'tipo_proposicao' => $proposicao->tipoProposicao->nome ?? '',
            'nome_municipio' => config('app.municipio_nome', ''),
            'nome_camara' => config('app.camara_nome', ''),
            'legislatura_atual' => config('app.legislatura_atual', ''),
            'sessao_legislativa' => config('app.sessao_legislativa', '')
        ];
    }
    
    private function substituirVariaveisViaOnlyOffice(
        ProposicaoTemplateInstance $instance, 
        array $variaveis
    ): void {
        try {
            // Verificar se OnlyOffice Document Builder está configurado
            $builderUrl = config('onlyoffice.builder_url');
            if (!$builderUrl) {
                // Fallback: substituição simples sem OnlyOffice
                $this->substituirVariaveisSimples($instance, $variaveis);
                return;
            }
            
            // Usar OnlyOffice Document Builder API para substituição preservando formatação
            $builderScript = $this->gerarScriptSubstituicao($variaveis);
            
            $response = Http::timeout(60)->post($builderUrl, [
                'document' => base64_encode(Storage::get($instance->arquivo_instance_path)),
                'script' => $builderScript,
                'outputFormat' => 'docx'
            ]);
            
            if ($response->successful()) {
                $documentoProcessado = base64_decode($response->json('document'));
                Storage::put($instance->arquivo_instance_path, $documentoProcessado);
            } else {
                // Fallback em caso de erro
                $this->substituirVariaveisSimples($instance, $variaveis);
            }
            
        } catch (Exception $e) {
            // Log::warning('Erro ao processar variáveis via OnlyOffice, usando fallback', [
            //     'instance_id' => $instance->id,
            //     'erro' => $e->getMessage()
            // ]);
            
            // Fallback: substituição simples
            $this->substituirVariaveisSimples($instance, $variaveis);
        }
    }
    
    private function substituirVariaveisSimples(ProposicaoTemplateInstance $instance, array $variaveis): void
    {
        // Substituição simples para arquivos de texto
        $conteudo = Storage::get($instance->arquivo_instance_path);
        
        foreach ($variaveis as $nome => $valor) {
            $conteudo = str_replace('{' . $nome . '}', $valor, $conteudo);
        }
        
        Storage::put($instance->arquivo_instance_path, $conteudo);
    }
    
    private function gerarScriptSubstituicao(array $variaveis): string
    {
        $substituicoes = [];
        foreach ($variaveis as $nome => $valor) {
            // Escapar aspas e caracteres especiais
            $valorEscapado = addslashes($valor);
            $substituicoes[] = "oDocument.SearchAndReplace('{{{$nome}}}', '{$valorEscapado}', true);";
        }
        
        return "
            var oDocument = Api.GetDocument();
            " . implode("\n", $substituicoes) . "
            builder.SaveFile('docx', 'output.docx');
            builder.CloseFile();
        ";
    }
    
    public function obterConfiguracaoOnlyOffice(ProposicaoTemplateInstance $instance): array
    {
        return [
            "document" => [
                "fileType" => "docx",
                "key" => $instance->document_key,
                "title" => "Proposição {$instance->proposicao->numero} - {$instance->template->nome}",
                "url" => route('onlyoffice.serve-instance', $instance->id),
            ],
            "editorConfig" => [
                "callbackUrl" => route('api.onlyoffice.callback.instance', $instance->id),
                "mode" => "edit",
                "lang" => "pt-BR",
                "user" => [
                    "id" => auth()->id(),
                    "name" => auth()->user()->name
                ],
                "customization" => [
                    "forcesave" => true,
                    "autosave" => false
                ]
            ]
        ];
    }
    
    public function finalizarEdicao(ProposicaoTemplateInstance $instance): void
    {
        $instance->update(['status' => 'finalizado']);
    }
    
    public function excluirInstancia(ProposicaoTemplateInstance $instance): bool
    {
        try {
            // Remover arquivo físico
            if ($instance->arquivo_instance_path && Storage::exists($instance->arquivo_instance_path)) {
                Storage::delete($instance->arquivo_instance_path);
            }
            
            // Remover registro
            $instance->delete();
            
            return true;
            
        } catch (Exception $e) {
            // Log::error('Erro ao excluir instância de template', [
            //     'instance_id' => $instance->id,
            //     'erro' => $e->getMessage()
            // ]);
            return false;
        }
    }
    
    public function obterStatusInstance(ProposicaoTemplateInstance $instance): string
    {
        return match($instance->status) {
            'preparando' => 'Preparando documento...',
            'pronto' => 'Pronto para edição',
            'editando' => 'Em edição',
            'finalizado' => 'Finalizado',
            default => 'Status desconhecido'
        };
    }
}