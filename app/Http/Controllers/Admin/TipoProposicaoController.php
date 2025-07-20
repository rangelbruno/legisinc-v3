<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoProposicao;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class TipoProposicaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        try {
            $tipos = TipoProposicao::ordenados()->paginate(15);
        } catch (\Exception $e) {
            // Se a tabela não existe, criar collection vazia para exibir mensagem
            $tipos = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                15,
                1,
                ['path' => request()->url()]
            );
        }
        
        return view('admin.tipo-proposicoes.index', compact('tipos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        try {
            $coresDisponiveis = TipoProposicao::CORES_DISPONIVEIS;
            $iconesDisponiveis = TipoProposicao::ICONES_DISPONIVEIS;
            $proximaOrdem = TipoProposicao::proximaOrdem();
            
            return view('admin.tipo-proposicoes.create', compact('coresDisponiveis', 'iconesDisponiveis', 'proximaOrdem'));
        } catch (\Exception $e) {
            // Fallback caso a tabela não exista ainda
            return redirect()->route('admin.dashboard')
                ->with('error', 'A tabela de tipos de proposição ainda não foi criada. Execute as migrations primeiro: php artisan migrate');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'codigo' => [
                    'required',
                    'string',
                    'max:50',
                    'regex:/^[a-z0-9_]+$/',
                    'unique:tipo_proposicoes,codigo'
                ],
                'nome' => 'required|string|max:200',
                'descricao' => 'nullable|string|max:1000',
                'icone' => 'required|string|in:' . implode(',', array_keys(TipoProposicao::ICONES_DISPONIVEIS)),
                'cor' => 'required|string|in:' . implode(',', array_keys(TipoProposicao::CORES_DISPONIVEIS)),
                'ativo' => 'boolean',
                'ordem' => 'required|integer|min:0',
                'template_padrao' => 'nullable|string',
                'configuracoes' => 'nullable|json',
            ], [
                'codigo.required' => 'O código é obrigatório.',
                'codigo.regex' => 'O código deve conter apenas letras minúsculas, números e underscores.',
                'codigo.unique' => 'Este código já está sendo usado.',
                'nome.required' => 'O nome é obrigatório.',
                'icone.required' => 'Selecione um ícone.',
                'cor.required' => 'Selecione uma cor.',
                'ordem.required' => 'A ordem é obrigatória.',
                'ordem.min' => 'A ordem deve ser um número positivo.',
                'configuracoes.json' => 'As configurações devem ser um JSON válido.',
            ]);
        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'A tabela de tipos de proposição ainda não foi criada. Execute as migrations primeiro: php artisan migrate');
        }

        $configuracoes = null;
        if ($request->filled('configuracoes')) {
            $configuracoes = json_decode($request->configuracoes, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['configuracoes' => 'JSON inválido nas configurações.'])->withInput();
            }
        }

        TipoProposicao::create([
            'codigo' => $request->codigo,
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'icone' => $request->icone,
            'cor' => $request->cor,
            'ativo' => $request->boolean('ativo', true),
            'ordem' => $request->ordem,
            'template_padrao' => $request->template_padrao,
            'configuracoes' => $configuracoes,
        ]);

        return redirect()->route('admin.tipo-proposicoes.index')
            ->with('success', 'Tipo de proposição criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(TipoProposicao $tipoProposicao): View
    {
        return view('admin.tipo-proposicoes.show', compact('tipoProposicao'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TipoProposicao $tipoProposicao): View
    {
        $coresDisponiveis = TipoProposicao::CORES_DISPONIVEIS;
        $iconesDisponiveis = TipoProposicao::ICONES_DISPONIVEIS;
        
        return view('admin.tipo-proposicoes.edit', compact('tipoProposicao', 'coresDisponiveis', 'iconesDisponiveis'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TipoProposicao $tipoProposicao): RedirectResponse
    {
        $request->validate([
            'codigo' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-z0-9_]+$/',
                Rule::unique('tipo_proposicoes', 'codigo')->ignore($tipoProposicao->id)
            ],
            'nome' => 'required|string|max:200',
            'descricao' => 'nullable|string|max:1000',
            'icone' => 'required|string|in:' . implode(',', array_keys(TipoProposicao::ICONES_DISPONIVEIS)),
            'cor' => 'required|string|in:' . implode(',', array_keys(TipoProposicao::CORES_DISPONIVEIS)),
            'ativo' => 'boolean',
            'ordem' => 'required|integer|min:0',
            'template_padrao' => 'nullable|string',
            'configuracoes' => 'nullable|json',
        ], [
            'codigo.required' => 'O código é obrigatório.',
            'codigo.regex' => 'O código deve conter apenas letras minúsculas, números e underscores.',
            'codigo.unique' => 'Este código já está sendo usado.',
            'nome.required' => 'O nome é obrigatório.',
            'icone.required' => 'Selecione um ícone.',
            'cor.required' => 'Selecione uma cor.',
            'ordem.required' => 'A ordem é obrigatória.',
            'ordem.min' => 'A ordem deve ser um número positivo.',
            'configuracoes.json' => 'As configurações devem ser um JSON válido.',
        ]);

        $configuracoes = null;
        if ($request->filled('configuracoes')) {
            $configuracoes = json_decode($request->configuracoes, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['configuracoes' => 'JSON inválido nas configurações.'])->withInput();
            }
        }

        $tipoProposicao->update([
            'codigo' => $request->codigo,
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'icone' => $request->icone,
            'cor' => $request->cor,
            'ativo' => $request->boolean('ativo', true),
            'ordem' => $request->ordem,
            'template_padrao' => $request->template_padrao,
            'configuracoes' => $configuracoes,
        ]);

        return redirect()->route('admin.tipo-proposicoes.index')
            ->with('success', 'Tipo de proposição atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipoProposicao $tipoProposicao): JsonResponse
    {
        // TODO: Verificar se o tipo está sendo usado em proposições
        // $proposicoesCount = $tipoProposicao->proposicoes()->count();
        // if ($proposicoesCount > 0) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => "Não é possível excluir este tipo pois existem {$proposicoesCount} proposições vinculadas."
        //     ], 400);
        // }

        $nome = $tipoProposicao->nome;
        $tipoProposicao->delete();

        return response()->json([
            'success' => true,
            'message' => "Tipo '{$nome}' excluído com sucesso!"
        ]);
    }

    /**
     * Toggle the active status of the specified resource.
     */
    public function toggleStatus(TipoProposicao $tipoProposicao): JsonResponse
    {
        $novoStatus = !$tipoProposicao->ativo;
        $tipoProposicao->update(['ativo' => $novoStatus]);

        return response()->json([
            'success' => true,
            'novo_status' => $novoStatus,
            'message' => $novoStatus ? 'Tipo ativado!' : 'Tipo desativado!'
        ]);
    }

    /**
     * Get tipos for AJAX dropdown
     */
    public function getParaDropdown(): JsonResponse
    {
        $tipos = TipoProposicao::getParaDropdown();
        
        return response()->json($tipos);
    }

    /**
     * Validate unique code via AJAX
     */
    public function validarCodigo(Request $request): JsonResponse
    {
        $codigo = $request->get('codigo');
        $excluirId = $request->get('excluir_id');
        
        $existe = TipoProposicao::codigoExiste($codigo, $excluirId);
        
        return response()->json([
            'disponivel' => !$existe,
            'message' => $existe ? 'Este código já está sendo usado.' : 'Código disponível.'
        ]);
    }

    /**
     * Reorder tipos via AJAX
     */
    public function reordenar(Request $request): JsonResponse
    {
        $request->validate([
            'tipos' => 'required|array',
            'tipos.*.id' => 'required|exists:tipo_proposicoes,id',
            'tipos.*.ordem' => 'required|integer|min:0',
        ]);

        foreach ($request->tipos as $tipoData) {
            TipoProposicao::where('id', $tipoData['id'])
                ->update(['ordem' => $tipoData['ordem']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ordem atualizada com sucesso!'
        ]);
    }

    /**
     * Bulk actions (activate/deactivate multiple types)
     */
    public function acoesBulk(Request $request): JsonResponse
    {
        $request->validate([
            'acao' => 'required|in:ativar,desativar,excluir',
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:tipo_proposicoes,id'
        ]);

        $tipos = TipoProposicao::whereIn('id', $request->ids);
        $count = $tipos->count();

        switch ($request->acao) {
            case 'ativar':
                $tipos->update(['ativo' => true]);
                $message = "{$count} tipo(s) ativado(s) com sucesso!";
                break;
                
            case 'desativar':
                $tipos->update(['ativo' => false]);
                $message = "{$count} tipo(s) desativado(s) com sucesso!";
                break;
                
            case 'excluir':
                // TODO: Verificar se algum tipo está sendo usado
                $tipos->delete();
                $message = "{$count} tipo(s) excluído(s) com sucesso!";
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
}