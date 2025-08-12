<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Parametro;
use App\Models\GrupoParametro;
use App\Services\Admin\ParametroService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ParametroController extends Controller
{
    public function __construct(
        private readonly ParametroService $parametroService
    ) {
        // Middleware será aplicado nas rotas
    }

    /**
     * Listar todos os parâmetros
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filtros = [
                'search' => $request->get('search'),
                'grupo_parametro_id' => $request->get('grupo_parametro_id'),
                'tipo_parametro_id' => $request->get('tipo_parametro_id'),
                'ativo' => $request->get('ativo', true)
            ];

            $parametros = $this->parametroService->listarParametros($filtros);

            return response()->json([
                'success' => true,
                'data' => $parametros
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao listar parâmetros via API', [
                //     'error' => $e->getMessage(),
                //     'trace' => $e->getTraceAsString()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar parâmetros: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter parâmetro por código
     */
    public function show(string $codigo): JsonResponse
    {
        try {
            $parametro = Parametro::where('codigo', $codigo)
                ->where('ativo', true)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => [
                    'codigo' => $parametro->codigo,
                    'nome' => $parametro->nome,
                    'valor' => $parametro->valor_formatado,
                    'tipo' => $parametro->tipoParametro->codigo,
                    'grupo' => $parametro->grupoParametro->codigo
                ]
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao obter parâmetro via API', [
                //     'codigo' => $codigo,
                //     'error' => $e->getMessage(),
                //     'trace' => $e->getTraceAsString()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Parâmetro não encontrado.'
            ], 404);
        }
    }

    /**
     * Obter parâmetros por grupo
     */
    public function porGrupo(string $codigoGrupo): JsonResponse
    {
        try {
            $grupo = GrupoParametro::where('codigo', $codigoGrupo)
                ->where('ativo', true)
                ->firstOrFail();

            $parametros = Parametro::where('grupo_parametro_id', $grupo->id)
                ->where('ativo', true)
                ->where('visivel', true)
                ->with(['tipoParametro'])
                ->ordenados()
                ->get();

            $resultado = [];
            foreach ($parametros as $parametro) {
                $resultado[$parametro->codigo] = $parametro->valor_formatado;
            }

            return response()->json([
                'success' => true,
                'data' => $resultado
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao obter parâmetros por grupo via API', [
                //     'codigo_grupo' => $codigoGrupo,
                //     'error' => $e->getMessage(),
                //     'trace' => $e->getTraceAsString()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Grupo não encontrado.'
            ], 404);
        }
    }

    /**
     * Atualizar parâmetros por grupo
     */
    public function atualizarPorGrupo(Request $request, string $codigoGrupo): JsonResponse
    {
        $request->validate([
            'parametros' => 'required|array',
            'parametros.*' => 'required|string'
        ]);

        try {
            $grupo = GrupoParametro::where('codigo', $codigoGrupo)
                ->where('ativo', true)
                ->firstOrFail();

            $parametros = $request->get('parametros');
            $resultado = $this->parametroService->atualizarParametrosPorGrupo($grupo->id, $parametros);

            return response()->json([
                'success' => true,
                'message' => 'Parâmetros atualizados com sucesso!',
                'atualizados' => $resultado['atualizados'],
                'erros' => $resultado['erros']
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao atualizar parâmetros por grupo via API', [
                //     'codigo_grupo' => $codigoGrupo,
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
     * Atualizar valor de um parâmetro específico
     */
    public function atualizarValor(Request $request, string $codigo): JsonResponse
    {
        $request->validate([
            'valor' => 'required|string'
        ]);

        try {
            $parametro = Parametro::where('codigo', $codigo)
                ->where('ativo', true)
                ->where('editavel', true)
                ->firstOrFail();

            $resultado = $this->parametroService->atualizarValorParametro($parametro->id, $request->get('valor'));

            return response()->json([
                'success' => true,
                'message' => 'Parâmetro atualizado com sucesso!',
                'data' => [
                    'codigo' => $parametro->codigo,
                    'valor' => $resultado['valor'],
                    'valor_formatado' => $resultado['valor_formatado']
                ]
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao atualizar valor do parâmetro via API', [
                //     'codigo' => $codigo,
                //     'valor' => $request->get('valor'),
                //     'error' => $e->getMessage(),
                //     'trace' => $e->getTraceAsString()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar parâmetro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar grupos de parâmetros
     */
    public function grupos(): JsonResponse
    {
        try {
            $grupos = GrupoParametro::where('ativo', true)
                ->with(['parametrosAtivos'])
                ->ordenados()
                ->get();

            $resultado = $grupos->map(function ($grupo) {
                return [
                    'codigo' => $grupo->codigo,
                    'nome' => $grupo->nome,
                    'descricao' => $grupo->descricao,
                    'icone' => $grupo->icone,
                    'cor' => $grupo->cor,
                    'parametros_count' => $grupo->parametros_ativos_count
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $resultado
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao listar grupos via API', [
                //     'error' => $e->getMessage(),
                //     'trace' => $e->getTraceAsString()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar grupos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validar valor antes de salvar
     */
    public function validarValor(Request $request): JsonResponse
    {
        $request->validate([
            'codigo' => 'required|string|exists:parametros,codigo',
            'valor' => 'required|string'
        ]);

        try {
            $parametro = Parametro::where('codigo', $request->get('codigo'))
                ->where('ativo', true)
                ->firstOrFail();

            $resultado = $this->parametroService->validarValorParametro(
                $parametro->tipo_parametro_id,
                $request->get('valor'),
                $parametro->regras_validacao ?? []
            );

            return response()->json([
                'success' => true,
                'valido' => $resultado['valido'],
                'erros' => $resultado['erros']
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao validar valor via API', [
                //     'codigo' => $request->get('codigo'),
                //     'valor' => $request->get('valor'),
                //     'error' => $e->getMessage(),
                //     'trace' => $e->getTraceAsString()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao validar valor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter estatísticas dos parâmetros
     */
    public function estatisticas(): JsonResponse
    {
        try {
            $estatisticas = $this->parametroService->obterEstatisticas();

            return response()->json([
                'success' => true,
                'data' => $estatisticas
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao obter estatísticas via API', [
                //     'error' => $e->getMessage(),
                //     'trace' => $e->getTraceAsString()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter estatísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportar parâmetros para backup
     */
    public function exportar(Request $request): JsonResponse
    {
        try {
            $grupoId = $request->get('grupo_id');
            $backup = $this->parametroService->exportarParametros($grupoId);

            return response()->json([
                'success' => true,
                'data' => $backup,
                'filename' => 'parametros_backup_' . now()->format('Y_m_d_H_i_s') . '.json'
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao exportar parâmetros via API', [
                //     'grupo_id' => $request->get('grupo_id'),
                //     'error' => $e->getMessage(),
                //     'trace' => $e->getTraceAsString()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao exportar parâmetros: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Importar parâmetros de backup
     */
    public function importar(Request $request): JsonResponse
    {
        $request->validate([
            'backup_data' => 'required|array'
        ]);

        try {
            $resultado = $this->parametroService->importarParametros($request->get('backup_data'));

            return response()->json([
                'success' => true,
                'message' => 'Parâmetros importados com sucesso!',
                'importados' => $resultado['importados'],
                'erros' => $resultado['erros']
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao importar parâmetros via API', [
                //     'backup_data' => $request->get('backup_data'),
                //     'error' => $e->getMessage(),
                //     'trace' => $e->getTraceAsString()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao importar parâmetros: ' . $e->getMessage()
            ], 500);
        }
    }
}