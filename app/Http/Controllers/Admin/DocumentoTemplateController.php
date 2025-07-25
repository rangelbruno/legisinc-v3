<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Documento\DocumentoModelo;
use App\Models\TipoProposicao;
use App\Services\Documento\DocumentoModeloService;

class DocumentoTemplateController extends Controller
{
    public function __construct(
        private DocumentoModeloService $documentoModeloService
    ) {}
    
    public function index(Request $request)
    {
        $query = DocumentoModelo::with(['tipoProposicao', 'creator'])
            ->where('is_template', true);
            
        // Filtro por categoria
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }
        
        // Filtro por tipo de proposição
        if ($request->filled('tipo_proposicao_id')) {
            $query->where('tipo_proposicao_id', $request->tipo_proposicao_id);
        }
        
        $templates = $query->orderBy('categoria')
                          ->orderBy('ordem')
                          ->orderBy('nome')
                          ->paginate(20);
                          
        $categorias = DocumentoModelo::CATEGORIAS;
        $tiposProposicao = TipoProposicao::ativos()->ordenados()->get();
        $templatesPadrao = DocumentoModelo::TEMPLATES_PADRAO;
        
        return view('admin.documentos.templates.index', compact(
            'templates',
            'categorias',
            'tiposProposicao',
            'templatesPadrao'
        ));
    }
    
    public function create()
    {
        $tiposProposicao = TipoProposicao::ativos()->ordenados()->get();
        $categorias = DocumentoModelo::CATEGORIAS;
        
        return view('admin.documentos.templates.create', compact(
            'tiposProposicao',
            'categorias'
        ));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:1000',
            'template_id' => 'required|string|max:100|unique:documento_modelos,template_id',
            'tipo_proposicao_id' => 'nullable|exists:tipo_proposicoes,id',
            'categoria' => 'required|string|in:' . implode(',', array_keys(DocumentoModelo::CATEGORIAS)),
            'ordem' => 'required|integer|min:0',
            'icon' => 'nullable|string|max:100',
            'variaveis' => 'nullable|array',
            'variaveis.*' => 'string|max:100',
            'metadata' => 'nullable|array'
        ]);
        
        $validated['is_template'] = true;
        
        $template = $this->documentoModeloService->criarModelo($validated);
        
        return redirect()
            ->route('admin.documentos.templates.edit', $template)
            ->with('success', 'Template criado com sucesso! Agora você pode editá-lo online.');
    }
    
    public function edit(DocumentoModelo $template)
    {
        if (!$template->is_template) {
            abort(404, 'Este modelo não é um template.');
        }
        
        $tiposProposicao = TipoProposicao::ativos()->ordenados()->get();
        $categorias = DocumentoModelo::CATEGORIAS;
        
        return view('admin.documentos.templates.edit', compact(
            'template',
            'tiposProposicao',
            'categorias'
        ));
    }
    
    public function update(Request $request, DocumentoModelo $template)
    {
        if (!$template->is_template) {
            abort(404, 'Este modelo não é um template.');
        }
        
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:1000',
            'tipo_proposicao_id' => 'nullable|exists:tipo_proposicoes,id',
            'categoria' => 'required|string|in:' . implode(',', array_keys(DocumentoModelo::CATEGORIAS)),
            'ordem' => 'required|integer|min:0',
            'icon' => 'nullable|string|max:100',
            'variaveis' => 'nullable|array',
            'variaveis.*' => 'string|max:100',
            'metadata' => 'nullable|array',
            'ativo' => 'boolean'
        ]);
        
        $template->update($validated);
        
        return redirect()
            ->route('admin.documentos.templates.index')
            ->with('success', 'Template atualizado com sucesso!');
    }
    
    public function destroy(DocumentoModelo $template)
    {
        if (!$template->is_template) {
            abort(404, 'Este modelo não é um template.');
        }
        
        // Verificar se há documentos usando este template
        if ($template->instancias()->exists()) {
            return back()->withErrors(['Este template possui documentos associados e não pode ser excluído.']);
        }
        
        $template->delete();
        
        return redirect()
            ->route('admin.documentos.templates.index')
            ->with('success', 'Template excluído com sucesso!');
    }
    
    public function reordenar(Request $request)
    {
        $validated = $request->validate([
            'templates' => 'required|array',
            'templates.*.id' => 'required|exists:documento_modelos,id',
            'templates.*.ordem' => 'required|integer|min:0'
        ]);
        
        foreach ($validated['templates'] as $item) {
            DocumentoModelo::where('id', $item['id'])
                ->where('is_template', true)
                ->update(['ordem' => $item['ordem']]);
        }
        
        return response()->json(['success' => true]);
    }
    
    public function resetarPadrao()
    {
        // Executar o seeder para garantir que todos os templates padrão existem
        \Artisan::call('db:seed', [
            '--class' => 'DocumentoModeloTemplateSeeder',
            '--force' => true
        ]);
        
        return redirect()
            ->route('admin.documentos.templates.index')
            ->with('success', 'Templates padrão restaurados com sucesso!');
    }
}