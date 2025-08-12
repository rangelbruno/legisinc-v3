<?php

namespace App\Services\Template;

use App\Models\DocumentoTemplate;
use App\Models\TemplateVariavel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;
use Exception;

class TemplateDocumentService
{
    public function criarTemplate(array $data, UploadedFile $arquivo): DocumentoTemplate
    {
        DB::beginTransaction();
        
        try {
            // 1. Salvar arquivo original (preservado)
            $arquivoOriginalPath = $this->salvarArquivoOriginal($arquivo);
            
            // 2. Criar cópia de trabalho
            $arquivoModeloPath = $this->criarCopiaTrabalho($arquivoOriginalPath);
            
            // 3. Extrair variáveis do arquivo
            $variaveisEncontradas = $this->extrairVariaveis($arquivoOriginalPath);
            
            // 4. Criar registro no banco
            $template = DocumentoTemplate::create([
                'nome' => $data['nome'],
                'descricao' => $data['descricao'] ?? null,
                'tipo_proposicao_id' => $data['tipo_proposicao_id'],
                'arquivo_original_path' => $arquivoOriginalPath,
                'arquivo_modelo_path' => $arquivoModeloPath,
                'variaveis_mapeamento' => $variaveisEncontradas,
                'configuracao_onlyoffice' => $this->gerarConfiguracaoOnlyOffice(),
                'created_by' => Auth::id()
            ]);
            
            // 5. Processar variáveis encontradas
            $this->processarVariaveisTemplate($template);
            
            DB::commit();
            return $template;
            
        } catch (Exception $e) {
            DB::rollback();
            
            // Limpar arquivos criados em caso de erro
            if (isset($arquivoOriginalPath)) {
                Storage::delete($arquivoOriginalPath);
            }
            if (isset($arquivoModeloPath)) {
                Storage::delete($arquivoModeloPath);
            }
            
            throw $e;
        }
    }
    
    private function salvarArquivoOriginal(UploadedFile $arquivo): string
    {
        $nomeArquivo = 'templates/originais/' . time() . '_' . $arquivo->getClientOriginalName();
        return $arquivo->storeAs('public', $nomeArquivo);
    }
    
    private function criarCopiaTrabalho(string $arquivoOriginalPath): string
    {
        $arquivoOriginalCompleto = storage_path('app/' . $arquivoOriginalPath);
        $nomeCopiaTrabalhjo = 'templates/modelos/' . time() . '_modelo_' . basename($arquivoOriginalPath);
        $caminhoCompleto = storage_path('app/public/' . $nomeCopiaTrabalhjo);
        
        // Criar diretório se não existir
        $diretorio = dirname($caminhoCompleto);
        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0755, true);
        }
        
        // Copiar arquivo
        copy($arquivoOriginalCompleto, $caminhoCompleto);
        
        return 'public/' . $nomeCopiaTrabalhjo;
    }
    
    private function extrairVariaveis(string $arquivoPath): array
    {
        try {
            $arquivoCompleto = storage_path('app/' . $arquivoPath);
            
            // Verificar se o arquivo é .docx
            $extensao = pathinfo($arquivoCompleto, PATHINFO_EXTENSION);
            if (strtolower($extensao) !== 'docx') {
                // Para outros formatos, tentar ler como texto simples
                $conteudo = file_get_contents($arquivoCompleto);
                preg_match_all('/\{([^}]+)\}/', $conteudo, $matches);
                return array_unique($matches[1]);
            }
            
            // Usar PhpWord para extrair variáveis de .docx
            $phpWord = IOFactory::load($arquivoCompleto);
            $variaveisEncontradas = [];
            
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    $texto = $this->extrairTextoElemento($element);
                    preg_match_all('/\{([^}]+)\}/', $texto, $matches);
                    $variaveisEncontradas = array_merge($variaveisEncontradas, $matches[1]);
                }
            }
            
            return array_unique($variaveisEncontradas);
            
        } catch (Exception $e) {
            // Log::warning('Erro ao extrair variáveis do template', [
            //     'arquivo' => $arquivoPath,
            //     'erro' => $e->getMessage()
            // ]);
            
            // Retorna variáveis padrão em caso de erro
            return ['ementa', 'texto', 'nome_parlamentar', 'data'];
        }
    }
    
    private function extrairTextoElemento($element): string
    {
        $texto = '';
        
        // Diferentes tipos de elementos do PhpWord
        if (method_exists($element, 'getText')) {
            $texto .= $element->getText();
        } elseif (method_exists($element, 'getElements')) {
            foreach ($element->getElements() as $subElement) {
                $texto .= $this->extrairTextoElemento($subElement);
            }
        }
        
        return $texto;
    }
    
    private function processarVariaveisTemplate(DocumentoTemplate $template): void
    {
        $variaveisSistema = [
            'data', 'nome_parlamentar', 'cargo_parlamentar', 'email_parlamentar',
            'data_extenso', 'mes_atual', 'ano_atual', 'dia_atual', 'hora_atual',
            'numero_proposicao', 'tipo_proposicao', 'nome_municipio', 'nome_camara',
            'legislatura_atual', 'sessao_legislativa'
        ];
        
        foreach ($template->variaveis_mapeamento as $nomeVariavel) {
            $tipo = in_array($nomeVariavel, $variaveisSistema) ? 'sistema' : 'editavel';
            
            TemplateVariavel::create([
                'template_id' => $template->id,
                'nome_variavel' => $nomeVariavel,
                'tipo' => $tipo,
                'descricao' => $this->obterDescricaoVariavel($nomeVariavel),
                'obrigatoria' => in_array($nomeVariavel, ['ementa', 'texto'])
            ]);
        }
    }
    
    private function obterDescricaoVariavel(string $nomeVariavel): string
    {
        $descricoes = [
            'ementa' => 'Ementa da proposição',
            'texto' => 'Texto completo da proposição',
            'nome_parlamentar' => 'Nome do parlamentar',
            'cargo_parlamentar' => 'Cargo do parlamentar',
            'email_parlamentar' => 'Email do parlamentar',
            'data' => 'Data atual',
            'data_extenso' => 'Data por extenso',
            'numero_proposicao' => 'Número da proposição',
            'tipo_proposicao' => 'Tipo da proposição',
            'nome_municipio' => 'Nome do município',
            'nome_camara' => 'Nome da câmara'
        ];
        
        return $descricoes[$nomeVariavel] ?? 'Variável personalizada';
    }
    
    private function gerarConfiguracaoOnlyOffice(): array
    {
        return [
            'mode' => 'edit',
            'lang' => 'pt-BR',
            'customization' => [
                'forcesave' => true,
                'autosave' => false
            ]
        ];
    }
    
    public function atualizarTemplate(DocumentoTemplate $template, array $data, UploadedFile $arquivo = null): DocumentoTemplate
    {
        DB::beginTransaction();
        
        try {
            // Atualizar dados básicos
            $template->update([
                'nome' => $data['nome'],
                'descricao' => $data['descricao'] ?? $template->descricao,
            ]);
            
            // Se houver novo arquivo, processar
            if ($arquivo) {
                // Remover arquivos antigos
                if ($template->arquivo_original_path) {
                    Storage::delete($template->arquivo_original_path);
                }
                if ($template->arquivo_modelo_path) {
                    Storage::delete($template->arquivo_modelo_path);
                }
                
                // Processar novo arquivo
                $arquivoOriginalPath = $this->salvarArquivoOriginal($arquivo);
                $arquivoModeloPath = $this->criarCopiaTrabalho($arquivoOriginalPath);
                $variaveisEncontradas = $this->extrairVariaveis($arquivoOriginalPath);
                
                $template->update([
                    'arquivo_original_path' => $arquivoOriginalPath,
                    'arquivo_modelo_path' => $arquivoModeloPath,
                    'variaveis_mapeamento' => $variaveisEncontradas
                ]);
                
                // Reprocessar variáveis
                $template->variaveis()->delete();
                $this->processarVariaveisTemplate($template);
            }
            
            DB::commit();
            return $template->fresh();
            
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    
    public function excluirTemplate(DocumentoTemplate $template): bool
    {
        DB::beginTransaction();
        
        try {
            // Remover arquivos físicos
            if ($template->arquivo_original_path) {
                Storage::delete($template->arquivo_original_path);
            }
            if ($template->arquivo_modelo_path) {
                Storage::delete($template->arquivo_modelo_path);
            }
            
            // Remover registros do banco (variáveis são removidas automaticamente pelo CASCADE)
            $template->delete();
            
            DB::commit();
            return true;
            
        } catch (Exception $e) {
            DB::rollback();
            // Log::error('Erro ao excluir template', [
            //     'template_id' => $template->id,
            //     'erro' => $e->getMessage()
            // ]);
            return false;
        }
    }
}