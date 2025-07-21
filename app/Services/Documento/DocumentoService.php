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
        // Usar RTF para compatibilidade com Word sem dependências
        $extensao = $formato === 'pdf' ? 'rtf' : 'rtf'; // RTF funciona para ambos
        $nomeArquivo = "documento_" . $instancia->id . "_v" . $instancia->versao . "." . $extensao;
        $caminhoSaida = storage_path('app/documentos/editor/' . $nomeArquivo);
        
        $this->garantirDiretorio(dirname($caminhoSaida));

        // Processar conteúdo substituindo variáveis
        $conteudoProcessado = $this->substituirVariaveisNoConteudo($conteudoHtml, $variaveis);
        
        // Gerar arquivo RTF que o Word pode abrir perfeitamente
        $rtfContent = $this->gerarRTF($conteudoProcessado, $instancia->titulo ?? 'Documento');
        
        file_put_contents($caminhoSaida, $rtfContent);

        // Para PDF, tentamos converter usando LibreOffice se disponível
        if ($formato === 'pdf' && $this->libreOfficeDisponivel()) {
            try {
                $caminhoPdf = str_replace('.rtf', '.pdf', $caminhoSaida);
                $comando = sprintf(
                    'libreoffice --headless --convert-to pdf --outdir %s %s 2>/dev/null',
                    escapeshellarg(dirname($caminhoPdf)),
                    escapeshellarg($caminhoSaida)
                );
                
                exec($comando, $output, $returnCode);
                
                if ($returnCode === 0 && file_exists($caminhoPdf)) {
                    unlink($caminhoSaida); // Remove RTF temporário
                    $caminhoSaida = $caminhoPdf;
                    $nomeArquivo = str_replace('.rtf', '.pdf', $nomeArquivo);
                }
            } catch (\Exception $e) {
                \Log::warning('Erro na conversão para PDF: ' . $e->getMessage());
                // Continua com RTF se conversão falhar
            }
        }
        
        $instancia->update([
            'arquivo_gerado_path' => 'documentos/editor/' . $nomeArquivo,
            'arquivo_gerado_nome' => $nomeArquivo,
            'conteudo_personalizado' => $conteudoHtml,
            'variaveis_personalizadas' => $variaveis
        ]);
        
        return $caminhoSaida;
    }

    private function processarHtmlParaPhpWord($section, string $html): void
    {
        // Limpar HTML e processar elementos conhecidos
        $html = $this->limparHtml($html);
        
        // Criar um DOMDocument para processar o HTML
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        
        // Adicionar encoding e estrutura básica para melhor parsing
        $htmlCompleto = '<?xml encoding="utf-8" ?><div>' . $html . '</div>';
        $dom->loadHTML($htmlCompleto, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        
        $this->processarNoHtml($section, $dom->documentElement);
    }
    
    private function processarNoHtml($container, \DOMNode $node): void
    {
        foreach ($node->childNodes as $child) {
            switch ($child->nodeType) {
                case XML_TEXT_NODE:
                    $texto = trim($child->textContent);
                    if (!empty($texto)) {
                        $container->addText($texto, ['size' => 12, 'name' => 'Times New Roman']);
                    }
                    break;
                    
                case XML_ELEMENT_NODE:
                    $this->processarElementoHtml($container, $child);
                    break;
            }
        }
    }
    
    private function processarElementoHtml($container, \DOMElement $element): void
    {
        $tagName = strtolower($element->tagName);
        
        switch ($tagName) {
            case 'h1':
                $texto = $this->extrairTextoDoElemento($element);
                if (!empty($texto)) {
                    $container->addText($texto, [
                        'size' => 16,
                        'bold' => true,
                        'name' => 'Times New Roman'
                    ], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 240]);
                }
                break;
                
            case 'h2':
                $texto = $this->extrairTextoDoElemento($element);
                if (!empty($texto)) {
                    $container->addText($texto, [
                        'size' => 14,
                        'bold' => true,
                        'name' => 'Times New Roman'
                    ], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 200]);
                }
                break;
                
            case 'h3':
                $texto = $this->extrairTextoDoElemento($element);
                if (!empty($texto)) {
                    $container->addText($texto, [
                        'size' => 13,
                        'bold' => true,
                        'name' => 'Times New Roman'
                    ], ['spaceAfter' => 160]);
                }
                break;
                
            case 'p':
                $this->processarParagrafo($container, $element);
                break;
                
            case 'div':
                $this->processarDiv($container, $element);
                break;
                
            case 'strong':
            case 'b':
                $texto = $this->extrairTextoDoElemento($element);
                if (!empty($texto)) {
                    $container->addText($texto, ['bold' => true, 'size' => 12, 'name' => 'Times New Roman']);
                }
                break;
                
            case 'em':
            case 'i':
                $texto = $this->extrairTextoDoElemento($element);
                if (!empty($texto)) {
                    $container->addText($texto, ['italic' => true, 'size' => 12, 'name' => 'Times New Roman']);
                }
                break;
                
            case 'br':
                $container->addTextBreak();
                break;
                
            default:
                // Para elementos não conhecidos, processar o conteúdo interno
                $this->processarNoHtml($container, $element);
                break;
        }
    }
    
    private function processarParagrafo($container, \DOMElement $element): void
    {
        $style = $element->getAttribute('style');
        $alignment = \PhpOffice\PhpWord\SimpleType\Jc::BOTH; // Justificado por padrão
        
        // Verificar alinhamento no style
        if (strpos($style, 'text-align: center') !== false) {
            $alignment = \PhpOffice\PhpWord\SimpleType\Jc::CENTER;
        } elseif (strpos($style, 'text-align: right') !== false) {
            $alignment = \PhpOffice\PhpWord\SimpleType\Jc::RIGHT;
        } elseif (strpos($style, 'text-align: left') !== false) {
            $alignment = \PhpOffice\PhpWord\SimpleType\Jc::LEFT;
        }
        
        $textRun = $container->addTextRun(['alignment' => $alignment, 'spaceAfter' => 120]);
        
        $this->processarTextoFormatado($textRun, $element);
        
        // Se não há conteúdo, adicionar um parágrafo vazio
        if (trim($element->textContent) === '') {
            $container->addTextBreak();
        }
    }
    
    private function processarDiv($container, \DOMElement $element): void
    {
        $style = $element->getAttribute('style');
        
        // Verificar se é um div especial (como assinatura)
        if (strpos($style, 'text-align: right') !== false) {
            $textRun = $container->addTextRun(['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT, 'spaceAfter' => 120]);
            $this->processarTextoFormatado($textRun, $element);
        } else {
            // Div normal, processar conteúdo interno
            $this->processarNoHtml($container, $element);
        }
    }
    
    private function processarTextoFormatado($textRun, \DOMElement $element): void
    {
        foreach ($element->childNodes as $child) {
            switch ($child->nodeType) {
                case XML_TEXT_NODE:
                    $texto = $child->textContent;
                    if (!empty(trim($texto))) {
                        $textRun->addText($texto, ['size' => 12, 'name' => 'Times New Roman']);
                    }
                    break;
                    
                case XML_ELEMENT_NODE:
                    $this->processarElementoFormatado($textRun, $child);
                    break;
            }
        }
    }
    
    private function processarElementoFormatado($textRun, \DOMElement $element): void
    {
        $tagName = strtolower($element->tagName);
        $texto = $element->textContent;
        
        if (empty(trim($texto))) return;
        
        switch ($tagName) {
            case 'strong':
            case 'b':
                $textRun->addText($texto, ['bold' => true, 'size' => 12, 'name' => 'Times New Roman']);
                break;
                
            case 'em':
            case 'i':
                $textRun->addText($texto, ['italic' => true, 'size' => 12, 'name' => 'Times New Roman']);
                break;
                
            case 'span':
                // Verificar se é um placeholder de variável
                if ($element->getAttribute('class') === 'variable-placeholder') {
                    $textRun->addText($texto, ['size' => 12, 'name' => 'Times New Roman', 'color' => '0066CC']);
                } else {
                    $textRun->addText($texto, ['size' => 12, 'name' => 'Times New Roman']);
                }
                break;
                
            case 'br':
                $textRun->addTextBreak();
                break;
                
            default:
                $textRun->addText($texto, ['size' => 12, 'name' => 'Times New Roman']);
                break;
        }
    }
    
    private function extrairTextoDoElemento(\DOMElement $element): string
    {
        return trim($element->textContent);
    }
    
    private function limparHtml(string $html): string
    {
        // Remover tags de script, style e outros elementos desnecessários
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $html);
        $html = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/mi', '', $html);
        
        // Limpar comentários HTML
        $html = preg_replace('/<!--.*?-->/s', '', $html);
        
        // Converter entidades HTML
        $html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
        
        return $html;
    }

    private function gerarRTF(string $htmlContent, string $titulo): string
    {
        // Cabeçalho RTF
        $rtf = '{\\rtf1\\ansi\\deff0 {\\fonttbl {\\f0 Times New Roman;}}';
        $rtf .= '{\\*\\generator Sistema LegisInc;}';
        
        // Configurações de página (A4 com margens padrão)
        $rtf .= '\\paperw12240\\paperh15840'; // A4: 21cm x 29.7cm em twips (1440 twips = 1 inch)
        $rtf .= '\\margl1440\\margr1440\\margt1440\\margb1440'; // Margens de 1 inch
        
        // Começar documento
        $rtf .= '\\f0\\fs24\\sl288\\slmult1'; // Times New Roman, 12pt, espaçamento 1.2 entre linhas
        
        // Processar conteúdo HTML
        $rtfContent = $this->converterHtmlParaRTF($htmlContent);
        
        $rtf .= $rtfContent;
        
        // Fechar documento RTF
        $rtf .= '}';
        
        return $rtf;
    }
    
    private function converterHtmlParaRTF(string $html): string
    {
        // Limpar HTML
        $html = $this->limparHtml($html);
        
        // Criar DOMDocument
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        
        $htmlCompleto = '<?xml encoding="utf-8" ?><div>' . $html . '</div>';
        $dom->loadHTML($htmlCompleto, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        
        return $this->processarNoParaRTF($dom->documentElement);
    }
    
    private function processarNoParaRTF(\DOMNode $node): string
    {
        $rtf = '';
        
        foreach ($node->childNodes as $child) {
            switch ($child->nodeType) {
                case XML_TEXT_NODE:
                    $texto = trim($child->textContent);
                    if (!empty($texto)) {
                        $rtf .= $this->escaparTextoRTF($texto);
                    }
                    break;
                    
                case XML_ELEMENT_NODE:
                    $rtf .= $this->processarElementoParaRTF($child);
                    break;
            }
        }
        
        return $rtf;
    }
    
    private function processarElementoParaRTF(\DOMElement $element): string
    {
        $tagName = strtolower($element->tagName);
        $rtf = '';
        
        switch ($tagName) {
            case 'h1':
                $texto = $this->escaparTextoRTF($element->textContent);
                // Verificar se tem alinhamento específico no style
                $style = $element->getAttribute('style');
                $align = '\\qc'; // Centralizado por padrão para H1
                if (strpos($style, 'text-align: right') !== false) {
                    $align = '\\qr';
                } elseif (strpos($style, 'text-align: left') !== false) {
                    $align = '\\ql';
                }
                $rtf .= '\\par\\sb240\\sa240' . $align . '\\fs32\\b ' . $texto . '\\b0\\fs24\\ql\\par'; // Com espaçamento antes e depois
                break;
                
            case 'h2':
                $texto = $this->escaparTextoRTF($element->textContent);
                $style = $element->getAttribute('style');
                $align = '\\qc'; // Centralizado por padrão para H2
                if (strpos($style, 'text-align: right') !== false) {
                    $align = '\\qr';
                } elseif (strpos($style, 'text-align: left') !== false) {
                    $align = '\\ql';
                }
                $rtf .= '\\par\\sb200\\sa200' . $align . '\\fs28\\b ' . $texto . '\\b0\\fs24\\ql\\par';
                break;
                
            case 'h3':
                $texto = $this->escaparTextoRTF($element->textContent);
                $style = $element->getAttribute('style');
                $align = '\\ql'; // Alinhado à esquerda por padrão para H3
                if (strpos($style, 'text-align: center') !== false) {
                    $align = '\\qc';
                } elseif (strpos($style, 'text-align: right') !== false) {
                    $align = '\\qr';
                }
                $rtf .= '\\par\\sb160\\sa160' . $align . '\\fs26\\b ' . $texto . '\\b0\\fs24\\ql\\par';
                break;
                
            case 'p':
                $rtf .= $this->processarParagrafoRTF($element);
                break;
                
            case 'div':
                $style = $element->getAttribute('style');
                if (strpos($style, 'text-align: right') !== false) {
                    $rtf .= '\\par\\sb120\\sa120\\qr'; // Alinhar à direita com espaçamento
                    $rtf .= $this->processarNoParaRTF($element);
                    $rtf .= '\\par';
                } elseif (strpos($style, 'text-align: center') !== false) {
                    $rtf .= '\\par\\sb120\\sa120\\qc'; // Centralizar com espaçamento
                    $rtf .= $this->processarNoParaRTF($element);
                    $rtf .= '\\par';
                } else {
                    // Div normal, processar conteúdo mas adicionar espaçamento se contém texto
                    $conteudo = trim($element->textContent);
                    if (!empty($conteudo)) {
                        $rtf .= '\\par\\sb120\\sa120';
                    }
                    $rtf .= $this->processarNoParaRTF($element);
                    if (!empty($conteudo)) {
                        $rtf .= '\\par';
                    }
                }
                break;
                
            case 'strong':
            case 'b':
                $texto = $this->escaparTextoRTF($element->textContent);
                $rtf .= '\\b ' . $texto . '\\b0';
                break;
                
            case 'em':
            case 'i':
                $texto = $this->escaparTextoRTF($element->textContent);
                $rtf .= '\\i ' . $texto . '\\i0';
                break;
                
            case 'br':
                $rtf .= '\\par\\sb60'; // Quebra de linha com pequeno espaçamento
                break;
                
            case 'span':
                if ($element->getAttribute('class') === 'variable-placeholder') {
                    $texto = $this->escaparTextoRTF($element->textContent);
                    $rtf .= '{\\cf1 ' . $texto . '}'; // Cor azul para variáveis
                } else {
                    $rtf .= $this->processarNoParaRTF($element);
                }
                break;
                
            default:
                $rtf .= $this->processarNoParaRTF($element);
                break;
        }
        
        return $rtf;
    }
    
    private function processarParagrafoRTF(\DOMElement $element): string
    {
        $style = $element->getAttribute('style');
        $rtf = '\\par\\sb120\\sa120'; // Espaçamento antes e depois de cada parágrafo
        
        // Definir alinhamento
        if (strpos($style, 'text-align: center') !== false) {
            $rtf .= '\\qc';
        } elseif (strpos($style, 'text-align: right') !== false) {
            $rtf .= '\\qr';
        } elseif (strpos($style, 'text-align: left') !== false) {
            $rtf .= '\\ql';
        } else {
            $rtf .= '\\qj'; // Justificado por padrão
        }
        
        $rtf .= $this->processarNoParaRTF($element);
        $rtf .= '\\par';
        
        return $rtf;
    }
    
    private function escaparTextoRTF(string $texto): string
    {
        // Escapar caracteres especiais do RTF
        $texto = str_replace('\\', '\\\\', $texto);
        $texto = str_replace('{', '\\{', $texto);
        $texto = str_replace('}', '\\}', $texto);
        
        // Converter caracteres acentuados para códigos RTF
        $texto = mb_convert_encoding($texto, 'Windows-1252', 'UTF-8');
        
        return $texto;
    }

    private function substituirVariaveisNoConteudo(string $conteudo, array $variaveis): string
    {
        foreach ($variaveis as $nome => $valor) {
            // Substituir placeholders do editor
            $conteudo = str_replace('[' . $nome . ']', $valor, $conteudo);
            // Substituir outros formatos
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