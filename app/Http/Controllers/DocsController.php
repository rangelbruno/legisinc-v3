<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use League\CommonMark\GithubFlavoredMarkdownConverter;

class DocsController extends Controller
{
    public function fluxoProposicoes()
    {
        $filePath = base_path('docs/FLUXO-PROPOSICOES-MERMAID.md');
        
        if (!File::exists($filePath)) {
            abort(404, 'Documentação não encontrada');
        }
        
        // Cache baseado na última modificação do arquivo
        $lastModified = File::lastModified($filePath);
        $cacheKey = 'docs.fluxo-proposicoes.' . $lastModified;
        
        // Buscar conteúdo processado do cache ou processar
        $htmlContent = Cache::remember($cacheKey, 3600, function () use ($filePath) {
            $content = File::get($filePath);
            
            // Converter Markdown para HTML
            $converter = new GithubFlavoredMarkdownConverter([
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
                'max_nesting_level' => 10
            ]);
            
            return $converter->convert($content)->getContent();
        });
        
        // Informações do arquivo
        $fileInfo = [
            'lastModified' => date('d/m/Y H:i:s', $lastModified),
            'size' => number_format(File::size($filePath) / 1024, 2) . ' KB',
            'path' => 'docs/FLUXO-PROPOSICOES-MERMAID.md'
        ];
        
        return view('docs.fluxo-proposicoes', compact('htmlContent', 'fileInfo'));
    }
}
