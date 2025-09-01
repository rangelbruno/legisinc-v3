<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TemplateUniversal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TemplateUniversalApiController extends Controller
{
    public function preview($id): JsonResponse
    {
        $template = TemplateUniversal::findOrFail($id);
        
        // Extrair conteúdo RTF e converter para texto limpo para preview
        $conteudoTexto = $this->rtfToText($template->conteudo);
        
        // Extrair variáveis do template
        $variaveis = [];
        if ($template->variaveis) {
            $variaveis = is_string($template->variaveis) 
                ? json_decode($template->variaveis, true) 
                : $template->variaveis;
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $template->id,
                'nome' => $template->nome,
                'descricao' => $template->descricao,
                'formato' => $template->formato,
                'conteudo' => $conteudoTexto,
                'variaveis' => $variaveis,
                'updated_at' => $template->updated_at->format('d/m/Y H:i'),
                'updated_by' => $template->updatedBy->name ?? 'Sistema'
            ]
        ]);
    }
    
    private function rtfToText($rtf)
    {
        if (empty($rtf)) {
            return '';
        }
        
        // Cópia para processamento
        $text = $rtf;
        
        // Remover dados hexadecimais de imagens (blocos longos de hex)
        $text = preg_replace('/[0-9a-f]{100,}/i', '', $text);
        
        // Remover cabeçalhos e metadados RTF
        $text = preg_replace('/\{\\\\rtf1.*?\\\\viewkind\d+\\\\uc\d+/', '', $text);
        $text = preg_replace('/\{\\\\fonttbl.*?\}\}/', '', $text);
        $text = preg_replace('/\{\\\\colortbl.*?\}/', '', $text);
        $text = preg_replace('/\{\\\\\\*\\\\generator.*?\}/', '', $text);
        
        // Converter caracteres especiais Unicode
        $text = preg_replace_callback('/\\\\u(\d+)\*/', function($matches) {
            $code = intval($matches[1]);
            if ($code == 176) return '°'; // símbolo de grau
            if ($code == 218) return 'Ú';
            if ($code == 225) return 'á';
            if ($code == 227) return 'ã';
            if ($code == 231) return 'ç';
            if ($code == 233) return 'é';
            if ($code == 234) return 'ê';
            if ($code == 237) return 'í';
            if ($code == 243) return 'ó';
            if ($code == 245) return 'õ';
            if ($code == 250) return 'ú';
            if ($code == 193) return 'Á';
            if ($code == 195) return 'Ã';
            if ($code == 199) return 'Ç';
            if ($code == 201) return 'É';
            if ($code == 202) return 'Ê';
            if ($code == 205) return 'Í';
            if ($code == 211) return 'Ó';
            if ($code == 213) return 'Õ';
            return mb_chr($code, 'UTF-8');
        }, $text);
        
        // Converter quebras de linha
        $text = str_replace('\\par', "\n", $text);
        $text = str_replace('\\line', "\n", $text);
        
        // Remover comandos de formatação RTF
        $text = preg_replace('/\\\\b\s/', '', $text); // negrito on
        $text = preg_replace('/\\\\b0\s/', '', $text); // negrito off
        $text = preg_replace('/\\\\i\s/', '', $text); // itálico on
        $text = preg_replace('/\\\\i0\s/', '', $text); // itálico off
        $text = preg_replace('/\\\\fs\d+\s?/', '', $text); // tamanho da fonte
        $text = preg_replace('/\\\\f\d+\s?/', '', $text); // fonte
        $text = preg_replace('/\\\\pard/', '', $text); // parágrafo padrão
        $text = preg_replace('/\\\\sa\d+/', '', $text); // espaçamento depois
        $text = preg_replace('/\\\\sl\d+/', '', $text); // espaçamento de linha
        $text = preg_replace('/\\\\slmult\d+/', '', $text); // multiplicador
        $text = preg_replace('/\\\\qc/', '', $text); // centralizado
        $text = preg_replace('/\\\\ql/', '', $text); // alinhado à esquerda
        $text = preg_replace('/\\\\qr/', '', $text); // alinhado à direita
        $text = preg_replace('/\\\\qj/', '', $text); // justificado
        $text = preg_replace('/\\\\lang\d+/', '', $text); // idioma
        $text = preg_replace('/\\\\viewkind\d+/', '', $text);
        $text = preg_replace('/\\\\uc\d+/', '', $text);
        
        // Remover outros comandos RTF
        $text = preg_replace('/\\\\[a-z]+\d*\s?/', '', $text);
        
        // Remover chaves
        $text = str_replace('{', '', $text);
        $text = str_replace('}', '', $text);
        
        // Limpar espaços extras e linhas vazias múltiplas
        $text = preg_replace('/[ \t]+/', ' ', $text); // múltiplos espaços para um
        $text = preg_replace('/\n\s*\n\s*\n/', "\n\n", $text); // máximo 2 quebras de linha
        
        // Remover caracteres isolados no início (como 'd' solto)
        $text = preg_replace('/^[a-z]\s+/i', '', $text);
        
        // Limpar linhas com apenas um caractere
        $lines = explode("\n", $text);
        $lines = array_filter($lines, function($line) {
            $line = trim($line);
            return strlen($line) > 1 || $line === '';
        });
        $text = implode("\n", $lines);
        
        // Se houver placeholder de imagem, adicionar no lugar certo
        if (strpos($text, '${imagem_cabecalho}') === false) {
            $text = '${imagem_cabecalho}' . "\n\n" . $text;
        }
        
        $text = trim($text);
        
        return $text;
    }
}