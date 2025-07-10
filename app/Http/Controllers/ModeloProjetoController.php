<?php

namespace App\Http\Controllers;

use App\Models\ModeloProjeto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Exception;

class ModeloProjetoController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', ModeloProjeto::class);

        $query = ModeloProjeto::with(['criadoPor'])
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('tipo_projeto')) {
            $query->porTipo($request->tipo_projeto);
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo);
        }

        if ($request->filled('busca')) {
            $query->where(function ($q) use ($request) {
                $q->where('nome', 'like', '%' . $request->busca . '%')
                  ->orWhere('descricao', 'like', '%' . $request->busca . '%');
            });
        }

        $modelos = $query->paginate(15);
        $tipos = ModeloProjeto::TIPOS_PROJETO;

        return view('admin.modelos.index', compact('modelos', 'tipos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', ModeloProjeto::class);

        $tipos = ModeloProjeto::TIPOS_PROJETO;
        
        return view('admin.modelos.create', compact('tipos'));
    }

    /**
     * Show the editor for creating a new model.
     */
    public function editor(Request $request): View
    {
        $this->authorize('create', ModeloProjeto::class);

        $tipos = ModeloProjeto::TIPOS_PROJETO;
        $tipoSelecionado = $request->get('tipo');
        
        if ($tipoSelecionado && !array_key_exists($tipoSelecionado, $tipos)) {
            abort(404);
        }
        
        return view('admin.modelos.editor', compact('tipos', 'tipoSelecionado'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', ModeloProjeto::class);

        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:1000',
            'tipo_projeto' => 'required|string|in:' . implode(',', array_keys(ModeloProjeto::TIPOS_PROJETO)),
            'conteudo_modelo' => 'required|string',
            'campos_variaveis' => 'nullable|array',
            'campos_variaveis.*.nome' => 'required|string|max:100',
            'campos_variaveis.*.descricao' => 'required|string|max:255',
            'ativo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Decodificar campos_variaveis se for string JSON
            $camposVariaveis = $request->campos_variaveis;
            if (is_string($camposVariaveis)) {
                $camposVariaveis = json_decode($camposVariaveis, true) ?: [];
            }
            
            $modelo = ModeloProjeto::create([
                'nome' => $request->nome,
                'descricao' => $request->descricao,
                'tipo_projeto' => $request->tipo_projeto,
                'conteudo_modelo' => $request->conteudo_modelo,
                'campos_variaveis' => $camposVariaveis,
                'ativo' => $request->boolean('ativo', true),
                'criado_por' => auth()->id(),
            ]);

            // Se for uma requisição AJAX, retornar JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Modelo criado com sucesso!',
                    'modelo' => $modelo
                ]);
            }

            return redirect()->route('modelos.index')
                ->with('success', 'Modelo criado com sucesso!');

        } catch (Exception $e) {
            // Se for uma requisição AJAX, retornar JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar modelo: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Erro ao criar modelo: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ModeloProjeto $modelo): View
    {
        $this->authorize('view', $modelo);

        return view('admin.modelos.show', compact('modelo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ModeloProjeto $modelo): View
    {
        $this->authorize('update', $modelo);

        $tipos = ModeloProjeto::TIPOS_PROJETO;
        
        return view('admin.modelos.edit', compact('modelo', 'tipos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ModeloProjeto $modelo): RedirectResponse
    {
        $this->authorize('update', $modelo);

        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:1000',
            'tipo_projeto' => 'required|string|in:' . implode(',', array_keys(ModeloProjeto::TIPOS_PROJETO)),
            'conteudo_modelo' => 'required|string',
            'campos_variaveis' => 'nullable|array',
            'campos_variaveis.*.nome' => 'required|string|max:100',
            'campos_variaveis.*.descricao' => 'required|string|max:255',
            'ativo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $modelo->update([
                'nome' => $request->nome,
                'descricao' => $request->descricao,
                'tipo_projeto' => $request->tipo_projeto,
                'conteudo_modelo' => $request->conteudo_modelo,
                'campos_variaveis' => $request->campos_variaveis ?: [],
                'ativo' => $request->boolean('ativo', true),
            ]);

            return redirect()->route('modelos.index')
                ->with('success', 'Modelo atualizado com sucesso!');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar modelo: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ModeloProjeto $modelo): RedirectResponse
    {
        $this->authorize('delete', $modelo);

        try {
            $modelo->delete();

            return redirect()->route('modelos.index')
                ->with('success', 'Modelo excluído com sucesso!');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao excluir modelo: ' . $e->getMessage());
        }
    }

    /**
     * Obter modelos por tipo (AJAX)
     */
    public function porTipo(Request $request): JsonResponse
    {
        $tipo = $request->get('tipo');

        if (!$tipo) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de projeto não informado'
            ], 400);
        }

        try {
            $modelos = ModeloProjeto::ativos()
                ->porTipo($tipo)
                ->select('id', 'nome', 'descricao')
                ->get();

            return response()->json([
                'success' => true,
                'modelos' => $modelos
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar modelos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter conteúdo do modelo (AJAX)
     */
    public function conteudo(ModeloProjeto $modelo): JsonResponse
    {
        try {
            $conteudo = $modelo->processarConteudo([
                'NOME_AUTOR' => auth()->user()->name,
                'NUMERO_PROJETO' => '[A ser definido]',
                'ANO_PROJETO' => date('Y'),
            ]);

            return response()->json([
                'success' => true,
                'conteudo' => $conteudo,
                'variaveis' => $modelo->getVariaveisDisponiveis()
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar modelo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Alternar status ativo/inativo
     */
    public function toggleStatus(ModeloProjeto $modelo): RedirectResponse
    {
        $this->authorize('update', $modelo);

        try {
            $modelo->update([
                'ativo' => !$modelo->ativo
            ]);

            $status = $modelo->ativo ? 'ativado' : 'desativado';
            
            return redirect()->back()
                ->with('success', "Modelo {$status} com sucesso!");

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao alterar status: ' . $e->getMessage());
        }
    }

    /**
     * Upload de imagem para o editor
     */
    public function uploadImage(Request $request): JsonResponse
    {
        $this->authorize('create', ModeloProjeto::class);

        try {
            $validator = Validator::make($request->all(), [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivo inválido. Use apenas imagens (JPEG, PNG, JPG, GIF, SVG) de até 2MB.'
                ], 400);
            }

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . uniqid() . '.' . $image->extension();
                
                // Criar diretório se não existir
                $uploadPath = public_path('uploads/modelos/images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                $image->move($uploadPath, $imageName);
                
                $imageUrl = asset('uploads/modelos/images/' . $imageName);
                
                return response()->json([
                    'success' => true,
                    'url' => $imageUrl,
                    'filename' => $imageName,
                    'message' => 'Imagem enviada com sucesso!'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Nenhuma imagem foi enviada.'
            ], 400);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer upload: ' . $e->getMessage()
            ], 500);
        }
    }
}