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
     * Show the editor for creating a new model (now using TipTap).
     */
    public function editor(Request $request): View
    {
        $this->authorize('create', ModeloProjeto::class);

        $tipos = ModeloProjeto::TIPOS_PROJETO;
        $tipoSelecionado = $request->get('tipo', 'contrato');
        $modelo = null;
        
        // Se for edição de modelo existente
        if ($request->has('id')) {
            $modelo = ModeloProjeto::findOrFail($request->get('id'));
            $this->authorize('update', $modelo);
            $tipoSelecionado = $modelo->tipo_projeto;
        }
        
        if ($tipoSelecionado && !array_key_exists($tipoSelecionado, $tipos)) {
            abort(404);
        }
        
        return view('admin.modelos.editor-tiptap', compact('tipos', 'tipoSelecionado', 'modelo'));
    }

    /**
     * Editor de modelos com Tiptap (nova versão)
     */
    public function editorTiptap(Request $request): View
    {
        $this->authorize('create', ModeloProjeto::class);

        $tipos = ModeloProjeto::TIPOS_PROJETO;
        $tipoSelecionado = $request->get('tipo', 'contrato');
        $modelo = null;
        
        // Se for edição de modelo existente
        if ($request->has('id')) {
            $modelo = ModeloProjeto::findOrFail($request->get('id'));
            $this->authorize('update', $modelo);
            $tipoSelecionado = $modelo->tipo_projeto;
        }
        
        if ($tipoSelecionado && !array_key_exists($tipoSelecionado, $tipos)) {
            abort(404);
        }
        
        return view('admin.modelos.editor-tiptap', compact('tipos', 'tipoSelecionado', 'modelo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $this->authorize('create', ModeloProjeto::class);

        // Determinar se é requisição JSON (do editor Tiptap) ou form tradicional
        $isJsonRequest = $request->expectsJson() || $request->isJson();
        
        $rules = [
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:1000',
            'tipo' => 'required|string|in:' . implode(',', array_keys(ModeloProjeto::TIPOS_PROJETO)),
            'conteudo' => 'required|string',
            'variaveis' => 'nullable|array',
            'ativo' => 'boolean',
        ];
        
        // Para compatibilidade com editor antigo
        if (!$isJsonRequest) {
            $rules['tipo_projeto'] = $rules['tipo'];
            $rules['conteudo_modelo'] = $rules['conteudo'];
            $rules['campos_variaveis'] = 'nullable|array';
            $rules['campos_variaveis.*.nome'] = 'required|string|max:100';
            $rules['campos_variaveis.*.descricao'] = 'required|string|max:255';
            unset($rules['tipo'], $rules['conteudo'], $rules['variaveis']);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if ($isJsonRequest) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Preparar dados baseado no tipo de requisição
            if ($isJsonRequest) {
                // Editor Tiptap
                $data = [
                    'nome' => $request->nome,
                    'descricao' => $request->descricao,
                    'tipo_projeto' => $request->tipo,
                    'conteudo_modelo' => $request->conteudo,
                    'campos_variaveis' => $this->processVariaveis($request->variaveis ?? []),
                    'ativo' => $request->boolean('ativo', true),
                    'criado_por' => auth()->id(),
                ];
            } else {
                // Editor antigo
                $camposVariaveis = $request->campos_variaveis;
                if (is_string($camposVariaveis)) {
                    $camposVariaveis = json_decode($camposVariaveis, true) ?: [];
                }
                
                $data = [
                    'nome' => $request->nome,
                    'descricao' => $request->descricao,
                    'tipo_projeto' => $request->tipo_projeto,
                    'conteudo_modelo' => $request->conteudo_modelo,
                    'campos_variaveis' => $camposVariaveis,
                    'ativo' => $request->boolean('ativo', true),
                    'criado_por' => auth()->id(),
                ];
            }
            
            $modelo = ModeloProjeto::create($data);

            // Resposta baseada no tipo de requisição
            if ($isJsonRequest) {
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
    public function update(Request $request, ModeloProjeto $modelo): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $modelo);

        // Determinar se é requisição JSON (do editor Tiptap) ou form tradicional
        $isJsonRequest = $request->expectsJson() || $request->isJson();
        
        $rules = [
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:1000',
            'tipo' => 'required|string|in:' . implode(',', array_keys(ModeloProjeto::TIPOS_PROJETO)),
            'conteudo' => 'required|string',
            'variaveis' => 'nullable|array',
            'ativo' => 'boolean',
        ];
        
        // Para compatibilidade com editor antigo
        if (!$isJsonRequest) {
            $rules['tipo_projeto'] = $rules['tipo'];
            $rules['conteudo_modelo'] = $rules['conteudo'];
            $rules['campos_variaveis'] = 'nullable|array';
            $rules['campos_variaveis.*.nome'] = 'required|string|max:100';
            $rules['campos_variaveis.*.descricao'] = 'required|string|max:255';
            unset($rules['tipo'], $rules['conteudo'], $rules['variaveis']);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if ($isJsonRequest) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Preparar dados baseado no tipo de requisição
            if ($isJsonRequest) {
                // Editor Tiptap
                $data = [
                    'nome' => $request->nome,
                    'descricao' => $request->descricao,
                    'tipo_projeto' => $request->tipo,
                    'conteudo_modelo' => $request->conteudo,
                    'campos_variaveis' => $this->processVariaveis($request->variaveis ?? []),
                    'ativo' => $request->boolean('ativo', true),
                ];
            } else {
                // Editor antigo
                $data = [
                    'nome' => $request->nome,
                    'descricao' => $request->descricao,
                    'tipo_projeto' => $request->tipo_projeto,
                    'conteudo_modelo' => $request->conteudo_modelo,
                    'campos_variaveis' => $request->campos_variaveis ?: [],
                    'ativo' => $request->boolean('ativo', true),
                ];
            }

            $modelo->update($data);

            // Resposta baseada no tipo de requisição
            if ($isJsonRequest) {
                return response()->json([
                    'success' => true,
                    'message' => 'Modelo atualizado com sucesso!',
                    'modelo' => $modelo->fresh()
                ]);
            }

            return redirect()->route('modelos.index')
                ->with('success', 'Modelo atualizado com sucesso!');

        } catch (Exception $e) {
            if ($isJsonRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao atualizar modelo: ' . $e->getMessage()
                ], 500);
            }
            
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

    /**
     * Processar variáveis do editor Tiptap para formato do banco
     */
    private function processVariaveis(array $variaveis): array
    {
        $processedVariaveis = [];
        
        foreach ($variaveis as $variavel) {
            $processedVariaveis[] = [
                'nome' => $variavel,
                'descricao' => $this->gerarDescricaoVariavel($variavel),
                'tipo' => 'texto',
                'obrigatorio' => true
            ];
        }
        
        return $processedVariaveis;
    }

    /**
     * Gerar descrição automática para variável baseada no nome
     */
    private function gerarDescricaoVariavel(string $nomeVariavel): string
    {
        $descricoes = [
            'nome' => 'Nome da pessoa',
            'cpf' => 'CPF da pessoa',
            'cnpj' => 'CNPJ da empresa',
            'endereco' => 'Endereço completo',
            'data' => 'Data do documento',
            'valor' => 'Valor em reais',
            'cidade' => 'Cidade',
            'tipo_contrato' => 'Tipo do contrato',
            'contratante_nome' => 'Nome do contratante',
            'contratante_cnpj' => 'CNPJ do contratante',
            'contratante_endereco' => 'Endereço do contratante',
            'contratado_nome' => 'Nome do contratado',
            'contratado_cpf' => 'CPF do contratado',
            'contratado_endereco' => 'Endereço do contratado',
            'objeto_contrato' => 'Objeto do contrato',
            'prazo_contrato' => 'Prazo do contrato',
            'valor_contrato' => 'Valor do contrato',
            'forma_pagamento' => 'Forma de pagamento',
            'requerente_nome' => 'Nome do requerente',
            'requerente_qualificacao' => 'Qualificação do requerente',
            'requerente_cpf' => 'CPF do requerente',
            'requerente_endereco' => 'Endereço do requerente',
            'autoridade' => 'Autoridade competente',
            'orgao' => 'Órgão responsável',
            'comarca' => 'Comarca',
            'fatos' => 'Descrição dos fatos',
            'fundamento_juridico' => 'Fundamentação jurídica',
            'pedido' => 'Pedido',
            'advogado_nome' => 'Nome do advogado',
            'advogado_oab' => 'Número da OAB',
            'advogado_estado' => 'Estado da OAB',
            'numero_projeto' => 'Número do projeto',
            'ano_projeto' => 'Ano do projeto',
            'ementa' => 'Ementa do projeto',
            'objeto_lei' => 'Objeto da lei',
            'definicao_a' => 'Primeira definição',
            'definicao_b' => 'Segunda definição'
        ];
        
        return $descricoes[$nomeVariavel] ?? ucfirst(str_replace('_', ' ', $nomeVariavel));
    }
}