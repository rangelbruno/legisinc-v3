<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class DocsController extends Controller
{
    public function fluxoProposicoes()
    {
        $filePath = base_path('docs/FLUXO-PROPOSICOES-MERMAID.md');
        
        if (!File::exists($filePath)) {
            abort(404, 'Documentação não encontrada');
        }
        
        $content = File::get($filePath);
        
        return view('docs.fluxo-proposicoes', compact('content'));
    }
}
