<?php

namespace App\Services\Documento;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class VariavelService
{
    public function extrairVariaveisDocumento(string $caminhoArquivo): array
    {
        if (!file_exists($caminhoArquivo)) {
            return [];
        }

        try {
            if (class_exists('\PhpOffice\PhpWord\TemplateProcessor')) {
                $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($caminhoArquivo);
                return $templateProcessor->getVariables();
            }
            
            return $this->extrairVariaveisManualmente($caminhoArquivo);
        } catch (\Exception $e) {
            \Log::warning('Erro ao extrair variáveis do documento: ' . $e->getMessage());
            return $this->definirVariaveisPadrao();
        }
    }

    public function extrairVariaveisDeUpload(UploadedFile $arquivo): array
    {
        $caminhoTemporario = $arquivo->path();
        return $this->extrairVariaveisDocumento($caminhoTemporario);
    }

    public function definirVariaveisPadrao(): array
    {
        return [
            'numero_proposicao' => 'Número da Proposição',
            'tipo_proposicao' => 'Tipo da Proposição',
            'ementa' => 'Ementa da Proposição',
            'autor_nome' => 'Nome do Autor',
            'autor_cargo' => 'Cargo do Autor',
            'data_criacao' => 'Data de Criação',
            'legislatura' => 'Legislatura Atual',
            'sessao_legislativa' => 'Sessão Legislativa'
        ];
    }

    public function obterVariaveisComValoresPadrao(array $variaveis): array
    {
        $resultado = [];
        $variaveisPadrao = $this->definirVariaveisPadrao();
        
        foreach ($variaveis as $variavel) {
            $nome = is_array($variavel) ? $variavel['nome'] : $variavel;
            $resultado[$nome] = $variaveisPadrao[$nome] ?? "Valor para {$nome}";
        }
        
        return $resultado;
    }

    public function preencherVariaveisComDadosProjeto(array $variaveis, $projeto): array
    {
        $resultado = $this->obterVariaveisComValoresPadrao($variaveis);
        
        $dados = [
            'numero_proposicao' => $projeto->numero_proposicao ?? $projeto->id ?? '',
            'tipo_proposicao' => $projeto->tipoProposicao->nome ?? '',
            'ementa' => $projeto->ementa ?? '',
            'autor_nome' => $projeto->creator->name ?? '',
            'autor_cargo' => 'Parlamentar',
            'data_criacao' => $projeto->created_at ? $projeto->created_at->format('d/m/Y') : date('d/m/Y'),
            'legislatura' => date('Y'),
            'sessao_legislativa' => date('Y'),
            'justificativa' => $projeto->justificativa ?? '',
            'artigos' => $projeto->artigos ?? '',
            'vigencia' => $projeto->vigencia ?? '',
            'gabinete' => $projeto->gabinete ?? '',
            'municipio' => $projeto->municipio ?? '',
            'estado' => $projeto->estado ?? 'BR',
            'data_atual' => date('d/m/Y')
        ];
        
        foreach ($resultado as $nome => $valorPadrao) {
            if (isset($dados[$nome])) {
                $resultado[$nome] = $dados[$nome];
            }
        }
        
        return $resultado;
    }

    public function validarVariaveisObrigatorias(array $variaveis): array
    {
        $variaveisObrigatorias = [
            'numero_proposicao',
            'tipo_proposicao',
            'ementa',
            'autor_nome'
        ];

        $faltando = [];
        foreach ($variaveisObrigatorias as $variavel) {
            if (!in_array($variavel, $variaveis)) {
                $faltando[] = $variavel;
            }
        }

        return $faltando;
    }

    public function formatarVariaveisParaExibicao(array $variaveis): array
    {
        $descricoes = $this->obterDescricoesVariaveis();
        $formatadas = [];

        foreach ($variaveis as $variavel) {
            $formatadas[] = [
                'nome' => $variavel,
                'descricao' => $descricoes[$variavel] ?? ucfirst(str_replace('_', ' ', $variavel)),
                'obrigatoria' => $this->isVariavelObrigatoria($variavel),
                'exemplo' => $this->obterExemploVariavel($variavel)
            ];
        }

        return $formatadas;
    }

    public function substituirVariaveisEmTexto(string $texto, array $valores): string
    {
        foreach ($valores as $variavel => $valor) {
            $texto = str_replace('${' . $variavel . '}', $valor, $texto);
            $texto = str_replace('{{' . $variavel . '}}', $valor, $texto);
        }

        return $texto;
    }

    private function extrairVariaveisManualmente(string $caminhoArquivo): array
    {
        if (pathinfo($caminhoArquivo, PATHINFO_EXTENSION) !== 'docx') {
            return [];
        }

        $variaveis = [];
        
        try {
            $zip = new \ZipArchive();
            if ($zip->open($caminhoArquivo) === TRUE) {
                $documentXml = $zip->getFromName('word/document.xml');
                
                if ($documentXml) {
                    preg_match_all('/\$\{([^}]+)\}/', $documentXml, $matches);
                    if (!empty($matches[1])) {
                        $variaveis = array_merge($variaveis, $matches[1]);
                    }
                    
                    preg_match_all('/\{\{([^}]+)\}\}/', $documentXml, $matches);
                    if (!empty($matches[1])) {
                        $variaveis = array_merge($variaveis, $matches[1]);
                    }
                }
                
                $zip->close();
            }
        } catch (\Exception $e) {
            \Log::warning('Erro na extração manual de variáveis: ' . $e->getMessage());
        }

        return array_unique($variaveis);
    }

    private function obterDescricoesVariaveis(): array
    {
        return [
            'numero_proposicao' => 'Número da Proposição',
            'tipo_proposicao' => 'Tipo da Proposição',
            'ementa' => 'Ementa da Proposição',
            'autor_nome' => 'Nome do Autor',
            'autor_cargo' => 'Cargo do Autor',
            'data_criacao' => 'Data de Criação',
            'data_atual' => 'Data Atual',
            'legislatura' => 'Legislatura Atual',
            'sessao_legislativa' => 'Sessão Legislativa',
            'gabinete' => 'Gabinete do Parlamentar',
            'municipio' => 'Município',
            'estado' => 'Estado',
            'justificativa' => 'Justificativa',
            'artigos' => 'Artigos da Proposição',
            'vigencia' => 'Data de Vigência'
        ];
    }

    private function isVariavelObrigatoria(string $variavel): bool
    {
        $obrigatorias = [
            'numero_proposicao',
            'tipo_proposicao',
            'ementa',
            'autor_nome'
        ];

        return in_array($variavel, $obrigatorias);
    }

    private function obterExemploVariavel(string $variavel): string
    {
        $exemplos = [
            'numero_proposicao' => '001/2024',
            'tipo_proposicao' => 'Projeto de Lei Ordinária',
            'ementa' => 'Dispõe sobre...',
            'autor_nome' => 'João Silva',
            'autor_cargo' => 'Vereador',
            'data_criacao' => date('d/m/Y'),
            'data_atual' => date('d/m/Y'),
            'legislatura' => date('Y'),
            'sessao_legislativa' => date('Y'),
            'gabinete' => 'Gabinete 01',
            'municipio' => 'São Paulo',
            'estado' => 'SP'
        ];

        return $exemplos[$variavel] ?? 'Exemplo para ' . $variavel;
    }

    public function validarFormatoDocumento(UploadedFile $arquivo): array
    {
        $errors = [];

        if ($arquivo->getClientOriginalExtension() !== 'docx') {
            $errors[] = 'Apenas arquivos .docx são aceitos';
        }

        if ($arquivo->getSize() > 10 * 1024 * 1024) { // 10MB
            $errors[] = 'Arquivo muito grande. Máximo 10MB';
        }

        $mimeType = $arquivo->getMimeType();
        $mimeTypesValidos = [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/zip'
        ];

        if (!in_array($mimeType, $mimeTypesValidos)) {
            $errors[] = 'Tipo de arquivo inválido';
        }

        return $errors;
    }
}