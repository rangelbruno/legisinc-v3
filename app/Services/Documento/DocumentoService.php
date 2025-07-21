<?php

namespace App\Services\Documento;

use App\Models\Documento\DocumentoModelo;
use App\Models\Documento\DocumentoInstancia;
use App\Models\Documento\DocumentoVersao;
use App\Models\Projeto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DocumentoService
{
    public function __construct(
        private VariavelService $variavelService
    ) {}

    public function criarInstanciaDocumento(int $projetoId, int $modeloId): DocumentoInstancia
    {
        $projeto = Projeto::findOrFail($projetoId);
        $modelo = DocumentoModelo::findOrFail($modeloId);
        
        $instancia = DocumentoInstancia::create([
            'projeto_id' => $projetoId,
            'modelo_id' => $modeloId,
            'status' => DocumentoInstancia::STATUS_RASCUNHO,
            'versao' => 1,
            'metadados' => $this->extrairMetadadosProjeto($projeto),
            'created_by' => auth()->id()
        ]);
        
        return $instancia;
    }

    public function gerarDocumentoComVariaveis(DocumentoInstancia $instancia): string
    {
        $modelo = $instancia->modelo;
        $caminhoModelo = storage_path('app/' . $modelo->arquivo_path);
        
        if (!file_exists($caminhoModelo)) {
            throw new \Exception('Arquivo modelo não encontrado: ' . $caminhoModelo);
        }

        $nomeArquivo = "documento_" . $instancia->id . "_v" . $instancia->versao . ".docx";
        $caminhoSaida = storage_path('app/documentos/instancias/' . $nomeArquivo);
        
        $this->garantirDiretorio(dirname($caminhoSaida));

        if (class_exists('\PhpOffice\PhpWord\TemplateProcessor')) {
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($caminhoModelo);
            
            foreach ($instancia->metadados as $variavel => $valor) {
                $templateProcessor->setValue($variavel, $valor);
            }
            
            $templateProcessor->saveAs($caminhoSaida);
        } else {
            copy($caminhoModelo, $caminhoSaida);
        }
        
        $instancia->update([
            'arquivo_path' => 'documentos/instancias/' . $nomeArquivo,
            'arquivo_nome' => $nomeArquivo
        ]);
        
        return $caminhoSaida;
    }

    public function uploadVersaoDocumento(DocumentoInstancia $instancia, UploadedFile $arquivo, ?string $comentarios = null): DocumentoVersao
    {
        $path = $arquivo->store('documentos/versoes');
        $novaVersao = $instancia->proximaVersao();
        
        $versao = DocumentoVersao::create([
            'instancia_id' => $instancia->id,
            'arquivo_path' => $path,
            'arquivo_nome' => $arquivo->getClientOriginalName(),
            'versao' => $novaVersao,
            'modificado_por' => auth()->id(),
            'comentarios' => $comentarios,
            'hash_arquivo' => hash_file('sha256', $arquivo->path())
        ]);
        
        $instancia->update([
            'status' => DocumentoInstancia::STATUS_LEGISLATIVO,
            'versao' => $novaVersao,
            'updated_by' => auth()->id()
        ]);
        
        return $versao;
    }

    public function converterParaPDF(DocumentoInstancia $instancia): string
    {
        $caminhoDocx = storage_path('app/' . $instancia->arquivo_path);
        
        if (!file_exists($caminhoDocx)) {
            throw new \Exception('Arquivo DOCX não encontrado: ' . $caminhoDocx);
        }
        
        $nomePdf = str_replace('.docx', '.pdf', $instancia->arquivo_nome);
        $caminhoPdf = storage_path('app/documentos/pdfs/' . $nomePdf);
        
        $this->garantirDiretorio(dirname($caminhoPdf));
        
        if ($this->libreOfficeDisponivel()) {
            $comando = sprintf(
                'libreoffice --headless --convert-to pdf --outdir %s %s 2>/dev/null',
                escapeshellarg(dirname($caminhoPdf)),
                escapeshellarg($caminhoDocx)
            );
            
            exec($comando, $output, $returnCode);
            
            if ($returnCode !== 0) {
                throw new \Exception('Erro na conversão para PDF. Código: ' . $returnCode);
            }
        } else {
            throw new \Exception('LibreOffice não está disponível para conversão de PDF');
        }
        
        return $caminhoPdf;
    }

    public function finalizarDocumento(DocumentoInstancia $instancia): bool
    {
        try {
            $caminhoPdf = $this->converterParaPDF($instancia);
            
            $instancia->update([
                'status' => DocumentoInstancia::STATUS_FINALIZADO,
                'updated_by' => auth()->id()
            ]);
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Erro ao finalizar documento: ' . $e->getMessage());
            return false;
        }
    }

    private function extrairMetadadosProjeto(Projeto $projeto): array
    {
        return [
            'numero_proposicao' => $projeto->id ?? '',
            'tipo_proposicao' => $projeto->tipoProposicao->nome ?? '',
            'ementa' => $projeto->ementa ?? '',
            'autor_nome' => $projeto->creator->name ?? '',
            'autor_cargo' => 'Parlamentar',
            'data_criacao' => $projeto->created_at ? $projeto->created_at->format('d/m/Y') : '',
            'legislatura' => date('Y'),
            'sessao_legislativa' => date('Y')
        ];
    }

    private function garantirDiretorio(string $caminho): void
    {
        if (!is_dir($caminho)) {
            mkdir($caminho, 0755, true);
        }
    }

    private function libreOfficeDisponivel(): bool
    {
        exec('which libreoffice', $output, $returnCode);
        return $returnCode === 0;
    }

    public function gerarDocumentoDoEditor(DocumentoInstancia $instancia, string $conteudoHtml, array $variaveis, string $formato = 'docx'): string
    {
        $nomeArquivo = "documento_" . $instancia->id . "_v" . $instancia->versao . "." . $formato;
        $caminhoSaida = storage_path('app/documentos/editor/' . $nomeArquivo);
        
        $this->garantirDiretorio(dirname($caminhoSaida));

        if (class_exists('\PhpOffice\PhpWord\PhpWord')) {
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $section = $phpWord->addSection();

            $conteudoProcessado = $this->substituirVariaveisNoConteudo($conteudoHtml, $variaveis);
            $conteudoTexto = strip_tags($conteudoProcessado);
            
            $section->addText($conteudoTexto);

            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($caminhoSaida);

            if ($formato === 'pdf') {
                $caminhoPdf = str_replace('.pdf', '.docx', $caminhoSaida);
                $objWriter->save($caminhoPdf);
                $caminhoSaida = $this->converterDocxParaPdf($caminhoPdf);
                $nomeArquivo = str_replace('.docx', '.pdf', $nomeArquivo);
            }
        } else {
            $conteudoProcessado = $this->substituirVariaveisNoConteudo($conteudoHtml, $variaveis);
            file_put_contents($caminhoSaida, $conteudoProcessado);
        }
        
        $instancia->update([
            'arquivo_gerado_path' => 'documentos/editor/' . $nomeArquivo,
            'arquivo_gerado_nome' => $nomeArquivo,
            'conteudo_personalizado' => $conteudoHtml,
            'variaveis_personalizadas' => $variaveis
        ]);
        
        return $caminhoSaida;
    }

    private function substituirVariaveisNoConteudo(string $conteudo, array $variaveis): string
    {
        foreach ($variaveis as $nome => $valor) {
            $conteudo = str_replace('${' . $nome . '}', $valor, $conteudo);
            $conteudo = str_replace('{{' . $nome . '}}', $valor, $conteudo);
        }
        return $conteudo;
    }

    private function converterDocxParaPdf(string $caminhoDocx): string
    {
        $caminhoPdf = str_replace('.docx', '.pdf', $caminhoDocx);
        
        if ($this->libreOfficeDisponivel()) {
            $comando = sprintf(
                'libreoffice --headless --convert-to pdf --outdir %s %s 2>/dev/null',
                escapeshellarg(dirname($caminhoPdf)),
                escapeshellarg($caminhoDocx)
            );
            
            exec($comando, $output, $returnCode);
            
            if ($returnCode === 0 && file_exists($caminhoPdf)) {
                unlink($caminhoDocx);
                return $caminhoPdf;
            }
        }
        
        return $caminhoDocx;
    }

    public function excluirInstancia(DocumentoInstancia $instancia): bool
    {
        try {
            if ($instancia->arquivo_path && Storage::exists($instancia->arquivo_path)) {
                Storage::delete($instancia->arquivo_path);
            }
            
            if ($instancia->arquivo_gerado_path && Storage::exists($instancia->arquivo_gerado_path)) {
                Storage::delete($instancia->arquivo_gerado_path);
            }
            
            foreach ($instancia->versoes as $versao) {
                if ($versao->arquivo_path && Storage::exists($versao->arquivo_path)) {
                    Storage::delete($versao->arquivo_path);
                }
            }
            
            $instancia->delete();
            return true;
        } catch (\Exception $e) {
            \Log::error('Erro ao excluir instância de documento: ' . $e->getMessage());
            return false;
        }
    }
}