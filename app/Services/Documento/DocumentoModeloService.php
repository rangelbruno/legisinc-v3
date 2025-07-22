<?php

namespace App\Services\Documento;

use App\Models\Documento\DocumentoModelo;
use App\Models\Documento\DocumentoInstancia;
use App\Models\Projeto;
use App\Services\OnlyOffice\OnlyOfficeService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DocumentoModeloService
{
    public function __construct(
        private OnlyOfficeService $onlyOfficeService
    ) {}
    
    public function criarModelo(array $dados): DocumentoModelo
    {
        // Criar arquivo inicial vazio
        $nomeArquivo = Str::slug($dados['nome']) . '.rtf';
        $documentKey = uniqid();
        
        $modelo = DocumentoModelo::create([
            'nome' => $dados['nome'],
            'descricao' => $dados['descricao'],
            'tipo_proposicao_id' => $dados['tipo_proposicao_id'] ?? null,
            'document_key' => $documentKey,
            'arquivo_nome' => $nomeArquivo,
            'arquivo_path' => null, // Será definido na primeira edição
            'arquivo_size' => 0,
            'versao' => '1.0',
            'variaveis' => $dados['variaveis'] ?? [],
            'icon' => $dados['icon'] ?? null,
            'created_by' => auth()->id()
        ]);
        
        return $modelo;
    }
    
    public function obterConfiguracaoEdicao(DocumentoModelo $modelo): array
    {
        $user = [
            'id' => auth()->id(),
            'name' => auth()->user()->name,
            'group' => $this->onlyOfficeService->obterGrupoUsuario(auth()->user())
        ];
        
        $fileUrl = route('onlyoffice.file.modelo', ['modelo' => $modelo->id]);
        
        return $this->onlyOfficeService->criarConfiguracao(
            $modelo->document_key,
            $modelo->arquivo_nome,
            $fileUrl,
            $user,
            'edit'
        );
    }
    
    public function criarInstanciaDoModelo(DocumentoModelo $modelo, int $projetoId): DocumentoInstancia
    {
        $projeto = Projeto::findOrFail($projetoId);
        $documentKey = uniqid();
        
        // Copiar arquivo do modelo se existir
        $nomeArquivo = "projeto_{$projetoId}_" . Str::slug($modelo->nome) . '.rtf';
        $pathDestino = "documentos/instancias/{$nomeArquivo}";
        
        if ($modelo->arquivo_path && Storage::exists($modelo->arquivo_path)) {
            Storage::copy($modelo->arquivo_path, $pathDestino);
        } else {
            // Criar arquivo vazio baseado no template
            $this->criarArquivoVazio($pathDestino);
        }
        
        // Criar instância
        $instancia = DocumentoInstancia::create([
            'projeto_id' => $projetoId,
            'modelo_id' => $modelo->id,
            'document_key' => $documentKey,
            'titulo' => "Documento baseado em: " . $modelo->nome,
            'arquivo_path' => $pathDestino,
            'arquivo_nome' => $nomeArquivo,
            'status' => 'rascunho',
            'versao' => 1,
            'metadados' => $this->extrairMetadadosProjeto($projeto),
            'variaveis_personalizadas' => $this->gerarVariaveisPersonalizadas($projeto),
            'created_by' => auth()->id()
        ]);
        
        return $instancia;
    }
    
    public function duplicarModelo(DocumentoModelo $modeloOriginal, array $dadosNovos): DocumentoModelo
    {
        $documentKey = uniqid();
        $nomeArquivo = Str::slug($dadosNovos['nome']) . '.rtf';
        
        $novoModelo = DocumentoModelo::create([
            'nome' => $dadosNovos['nome'],
            'descricao' => $dadosNovos['descricao'] ?? $modeloOriginal->descricao,
            'tipo_proposicao_id' => $dadosNovos['tipo_proposicao_id'] ?? $modeloOriginal->tipo_proposicao_id,
            'document_key' => $documentKey,
            'arquivo_nome' => $nomeArquivo,
            'arquivo_path' => null,
            'arquivo_size' => 0,
            'versao' => '1.0',
            'variaveis' => $dadosNovos['variaveis'] ?? $modeloOriginal->variaveis,
            'icon' => $dadosNovos['icon'] ?? $modeloOriginal->icon,
            'created_by' => auth()->id()
        ]);
        
        // Copiar arquivo se existir
        if ($modeloOriginal->arquivo_path && Storage::exists($modeloOriginal->arquivo_path)) {
            $novoPath = "documentos/modelos/{$nomeArquivo}";
            Storage::copy($modeloOriginal->arquivo_path, $novoPath);
            
            $novoModelo->update([
                'arquivo_path' => $novoPath,
                'arquivo_size' => $modeloOriginal->arquivo_size
            ]);
        }
        
        return $novoModelo;
    }
    
    public function atualizarMetadados(DocumentoModelo $modelo, array $metadados): DocumentoModelo
    {
        $modelo->update([
            'nome' => $metadados['nome'] ?? $modelo->nome,
            'descricao' => $metadados['descricao'] ?? $modelo->descricao,
            'tipo_proposicao_id' => $metadados['tipo_proposicao_id'] ?? $modelo->tipo_proposicao_id,
            'variaveis' => $metadados['variaveis'] ?? $modelo->variaveis,
            'icon' => $metadados['icon'] ?? $modelo->icon,
            'ativo' => $metadados['ativo'] ?? $modelo->ativo
        ]);
        
        return $modelo->fresh();
    }
    
    public function obterModelosDisponiveis(?int $tipoProposicaoId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = DocumentoModelo::where('ativo', true)
            ->orderBy('nome');
            
        if ($tipoProposicaoId) {
            $query->where(function($q) use ($tipoProposicaoId) {
                $q->where('tipo_proposicao_id', $tipoProposicaoId)
                  ->orWhereNull('tipo_proposicao_id');
            });
        }
        
        return $query->get();
    }
    
    private function extrairMetadadosProjeto(Projeto $projeto): array
    {
        return [
            'numero_proposicao' => $projeto->numero ?? 'A definir',
            'tipo_proposicao' => $projeto->tipo->nome ?? '',
            'ementa' => $projeto->ementa ?? '',
            'autor_nome' => $projeto->autor->name ?? '',
            'autor_cargo' => $projeto->autor->cargo ?? '',
            'data_criacao' => $projeto->created_at->format('d/m/Y'),
            'legislatura' => '2024-2028', // Configurável
            'sessao_legislativa' => date('Y')
        ];
    }
    
    private function gerarVariaveisPersonalizadas(Projeto $projeto): array
    {
        return [
            'numero_proposicao' => $projeto->numero ?? 'XXXX/YYYY',
            'tipo_proposicao' => $projeto->tipo->nome ?? 'PROJETO DE LEI',
            'ementa' => $projeto->ementa ?? '[EMENTA DO PROJETO]',
            'autor_nome' => $projeto->autor->name ?? '[NOME DO AUTOR]',
            'autor_cargo' => $projeto->autor->cargo ?? '[CARGO DO AUTOR]',
            'data_atual' => now()->format('d/m/Y'),
            'ano_atual' => date('Y'),
            'cidade' => 'São Paulo', // Configurável
            'orgao' => 'Câmara Municipal' // Configurável
        ];
    }
    
    private function criarArquivoVazio(string $path): void
    {
        // Criar um documento RTF básico
        $conteudoBase = '{\rtf1\ansi\deff0 {\fonttbl {\f0 Times New Roman;}}
{\colortbl;\red0\green0\blue0;}
\f0\fs24

{\qc\b\fs28 Novo Documento\par}
\par
Este é um documento base para começar a edição.\par
\par
Variáveis disponíveis:\par
- ${numero_proposicao}\par
- ${tipo_proposicao}\par
- ${autor_nome}\par
- ${autor_cargo}\par
- ${data_atual}\par
- ${ano_atual}\par
- ${cidade}\par
- ${orgao}\par
\par
Você pode usar essas variáveis no documento e elas serão substituídas automaticamente pelos valores do projeto.\par
}';
        
        Storage::disk('public')->put($path, $conteudoBase);
    }
}