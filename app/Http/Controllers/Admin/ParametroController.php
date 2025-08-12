<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Parametro;
use App\Models\GrupoParametro;
use App\Models\TipoParametro;
use App\Services\Admin\ParametroService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

/**
 * @deprecated Este controller foi substituído pelo novo sistema de parâmetros modulares
 * @see App\Http\Controllers\Parametro\ParametroController
 */
class ParametroController extends Controller
{
    public function __construct(
        private readonly ParametroService $parametroService
    ) {
        // Middleware será aplicado nas rotas
    }

    /**
     * Exibir listagem de parâmetros
     */
    public function index(Request $request)
    {
        try {
            $filtros = [
                'search' => $request->get('search'),
                'grupo_parametro_id' => $request->get('grupo_parametro_id'),
                'tipo_parametro_id' => $request->get('tipo_parametro_id'),
                'ativo' => $request->get('ativo'),
                'view_mode' => $request->get('view_mode', 'grid')
            ];

            $parametros = $this->parametroService->listarParametros($filtros);
            $grupos = GrupoParametro::ativos()->ordenados()->get();
            $tipos = TipoParametro::ativos()->ordenados()->get();

            $estatisticas = $this->parametroService->obterEstatisticas();

            // Se for requisição AJAX, retornar apenas os dados
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'parametros' => $parametros,
                    'estatisticas' => $estatisticas
                ]);
            }

            return view('admin.parametros.index', compact(
                'parametros',
                'grupos',
                'tipos',
                'filtros',
                'estatisticas'
            ));

        } catch (\Exception $e) {
            // Log::error('Erro ao carregar parâmetros', [
                //     'error' => $e->getMessage(),
                //     'trace' => $e->getTraceAsString()
            // ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao carregar parâmetros: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Erro ao carregar parâmetros: ' . $e->getMessage());
        }
    }

    /**
     * Exibir formulário de criação
     */
    public function create()
    {
        $grupos = GrupoParametro::ativos()->ordenados()->get();
        $tipos = TipoParametro::ativos()->ordenados()->get();

        return view('admin.parametros.create', compact('grupos', 'tipos'));
    }

    /**
     * Salvar novo parâmetro
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nome' => 'required|string|max:150',
            'codigo' => 'required|string|max:100|unique:parametros,codigo',
            'descricao' => 'nullable|string',
            'grupo_parametro_id' => 'required|exists:grupos_parametros,id',
            'tipo_parametro_id' => 'required|exists:tipos_parametros,id',
            'valor' => 'nullable|string',
            'valor_padrao' => 'nullable|string',
            'configuracao' => 'nullable|array',
            'regras_validacao' => 'nullable|array',
            'obrigatorio' => 'boolean',
            'editavel' => 'boolean',
            'visivel' => 'boolean',
            'ativo' => 'boolean',
            'ordem' => 'nullable|integer|min:0',
            'help_text' => 'nullable|string'
        ]);

        try {
            $dados = $request->all();
            $dados['obrigatorio'] = $request->has('obrigatorio');
            $dados['editavel'] = $request->has('editavel');
            $dados['visivel'] = $request->has('visivel');
            $dados['ativo'] = $request->has('ativo');

            $parametro = $this->parametroService->criarParametro($dados);

            return redirect()->route('admin.parametros.index')
                ->with('success', 'Parâmetro criado com sucesso!');

        } catch (\Exception $e) {
            // Log::error('Erro ao criar parâmetro', [
                //     'dados' => $request->all(),
                //     'error' => $e->getMessage(),
                //     'trace' => $e->getTraceAsString()
            // ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao criar parâmetro: ' . $e->getMessage());
        }
    }

    /**
     * Exibir parâmetro específico
     */
    public function show(int $id)
    {
        try {
            $parametro = $this->parametroService->obterParametroPorId($id);
            $historico = $this->parametroService->obterHistoricoParametro($id);

            return view('admin.parametros.show', compact('parametro', 'historico'));

        } catch (\Exception $e) {
            // Log::error('Erro ao carregar parâmetro', [
                //     'id' => $id,
                //     'error' => $e->getMessage(),
                //     'trace' => $e->getTraceAsString()
            // ]);

            return redirect()->route('admin.parametros.index')
                ->with('error', 'Parâmetro não encontrado.');
        }
    }

    /**
     * Exibir formulário de edição
     */
    public function edit(int $id)
    {
        try {
            $parametro = $this->parametroService->obterParametroPorId($id);
            $grupos = GrupoParametro::ativos()->ordenados()->get();
            $tipos = TipoParametro::ativos()->ordenados()->get();

            return view('admin.parametros.edit', compact('parametro', 'grupos', 'tipos'));

        } catch (\Exception $e) {
            // Log::error('Erro ao carregar parâmetro para edição', [
                //     'id' => $id,
                //     'error' => $e->getMessage(),
                //     'trace' => $e->getTraceAsString()
            // ]);

            return redirect()->route('admin.parametros.index')
                ->with('error', 'Parâmetro não encontrado.');
        }
    }

    /**
     * Atualizar parâmetro
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'nome' => 'required|string|max:150',
            'codigo' => 'required|string|max:100|unique:parametros,codigo,' . $id,
            'descricao' => 'nullable|string',
            'grupo_parametro_id' => 'required|exists:grupos_parametros,id',
            'tipo_parametro_id' => 'required|exists:tipos_parametros,id',
            'valor' => 'nullable|string',
            'valor_padrao' => 'nullable|string',
            'configuracao' => 'nullable|array',
            'regras_validacao' => 'nullable|array',
            'obrigatorio' => 'boolean',
            'editavel' => 'boolean',
            'visivel' => 'boolean',
            'ativo' => 'boolean',
            'ordem' => 'nullable|integer|min:0',
            'help_text' => 'nullable|string'
        ]);

        try {
            $dados = $request->all();
            $dados['obrigatorio'] = $request->has('obrigatorio');
            $dados['editavel'] = $request->has('editavel');
            $dados['visivel'] = $request->has('visivel');
            $dados['ativo'] = $request->has('ativo');

            $parametro = $this->parametroService->atualizarParametro($id, $dados);

            return redirect()->route('admin.parametros.index')
                ->with('success', 'Parâmetro atualizado com sucesso!');

        } catch (\Exception $e) {
            // Log::error('Erro ao atualizar parâmetro', [
                //     'id' => $id,
                //     'dados' => $request->all(),
                //     'error' => $e->getMessage(),
                //     'trace' => $e->getTraceAsString()
            // ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao atualizar parâmetro: ' . $e->getMessage());
        }
    }

    /**
     * Excluir parâmetro
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->parametroService->excluirParametro($id);

            return response()->json([
                'success' => true,
                'message' => 'Parâmetro excluído com sucesso!'
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao excluir parâmetro', [
                //     'id' => $id,
                //     'error' => $e->getMessage(),
                //     'trace' => $e->getTraceAsString()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir parâmetro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter parâmetros por grupo
     */
    public function porGrupo(Request $request, int $grupoId): JsonResponse
    {
        try {
            $parametros = $this->parametroService->obterParametrosPorGrupo($grupoId);

            return response()->json([
                'success' => true,
                'data' => $parametros
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao obter parâmetros por grupo', [
                //     'grupo_id' => $grupoId,
                //     'error' => $e->getMessage(),
                //     'trace' => $e->getTraceAsString()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter parâmetros: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualizar múltiplos parâmetros
     */
    public function atualizarMultiplos(Request $request): JsonResponse
    {
        $request->validate([
            'parametros' => 'required|array',
            'parametros.*' => 'required|string'
        ]);

        try {
            $parametros = $request->get('parametros');
            $resultado = $this->parametroService->atualizarMultiplosParametros($parametros);

            return response()->json([
                'success' => true,
                'message' => 'Parâmetros atualizados com sucesso!',
                'atualizados' => $resultado['atualizados'],
                'erros' => $resultado['erros']
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao atualizar múltiplos parâmetros', [
                //     'parametros' => $request->get('parametros'),
                //     'error' => $e->getMessage(),
                //     'trace' => $e->getTraceAsString()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar parâmetros: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Duplicar parâmetro
     */
    public function duplicar(int $id): JsonResponse
    {
        try {
            $parametro = $this->parametroService->duplicarParametro($id);

            return response()->json([
                'success' => true,
                'message' => 'Parâmetro duplicado com sucesso!',
                'parametro' => $parametro
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao duplicar parâmetro', [
                //     'id' => $id,
                //     'error' => $e->getMessage(),
                //     'trace' => $e->getTraceAsString()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao duplicar parâmetro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resetar parâmetro para valor padrão
     */
    public function resetarPadrao(int $id): JsonResponse
    {
        try {
            $parametro = $this->parametroService->resetarParametroParaPadrao($id);

            return response()->json([
                'success' => true,
                'message' => 'Parâmetro resetado para valor padrão!',
                'parametro' => $parametro
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao resetar parâmetro', [
                //     'id' => $id,
                //     'error' => $e->getMessage(),
                //     'trace' => $e->getTraceAsString()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao resetar parâmetro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Alterar status (ativo/inativo)
     */
    public function alterarStatus(int $id): JsonResponse
    {
        try {
            $parametro = $this->parametroService->alterarStatusParametro($id);

            return response()->json([
                'success' => true,
                'message' => 'Status alterado com sucesso!',
                'parametro' => $parametro
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao alterar status do parâmetro', [
                //     'id' => $id,
                //     'error' => $e->getMessage(),
                //     'trace' => $e->getTraceAsString()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao alterar status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reordenar parâmetros
     */
    public function reordenar(Request $request): JsonResponse
    {
        $request->validate([
            'parametros' => 'required|array',
            'parametros.*' => 'required|integer|exists:parametros,id'
        ]);

        try {
            $parametros = $request->get('parametros');
            $this->parametroService->reordenarParametros($parametros);

            return response()->json([
                'success' => true,
                'message' => 'Parâmetros reordenados com sucesso!'
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao reordenar parâmetros', [
                //     'parametros' => $request->get('parametros'),
                //     'error' => $e->getMessage(),
                //     'trace' => $e->getTraceAsString()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao reordenar parâmetros: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter configurações de tipo
     */
    public function obterConfiguracoesTipo(int $tipoId): JsonResponse
    {
        try {
            $tipo = TipoParametro::findOrFail($tipoId);
            $configuracoes = $tipo->configuracao_padrao_formatada;

            return response()->json([
                'success' => true,
                'configuracoes' => $configuracoes,
                'validacao' => $tipo->getValidationRules()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter configurações do tipo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validar valor do parâmetro
     */
    public function validarValor(Request $request): JsonResponse
    {
        $request->validate([
            'tipo_parametro_id' => 'required|exists:tipos_parametros,id',
            'valor' => 'required|string',
            'regras_adicionais' => 'nullable|array'
        ]);

        try {
            $resultado = $this->parametroService->validarValorParametro(
                $request->get('tipo_parametro_id'),
                $request->get('valor'),
                $request->get('regras_adicionais', [])
            );

            return response()->json([
                'success' => true,
                'valido' => $resultado['valido'],
                'erros' => $resultado['erros']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao validar valor: ' . $e->getMessage()
            ], 500);
        }
    }
}