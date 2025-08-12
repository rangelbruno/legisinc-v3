<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AI\AITextGenerationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AIController extends Controller
{
    protected AITextGenerationService $aiService;

    public function __construct(AITextGenerationService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Testar conexão com a IA
     */
    public function testarConexao(Request $request): JsonResponse
    {
        try {
            $configurationId = $request->input('configuration_id');
            
            // Se dados foram enviados via formulário, usar esses dados temporariamente
            $configData = null;
            if ($request->has(['ai_provider', 'ai_api_key', 'ai_model'])) {
                $configData = [
                    'name' => $request->input('name', 'Teste'),
                    'provider' => $request->input('ai_provider'),
                    'api_key' => $request->input('ai_api_key'),
                    'model' => $request->input('ai_model'),
                    'base_url' => $request->input('base_url'),
                    'max_tokens' => (int) ($request->input('ai_max_tokens') ?: 2000),
                    'temperature' => (float) ($request->input('ai_temperature') ?: 0.7),
                    'custom_prompt' => $request->input('ai_custom_prompt')
                ];
            }

            $resultado = $this->aiService->testarConexao($configData, $configurationId);

            return response()->json($resultado);

        } catch (\Exception $e) {
            // Log::error('Erro no teste de conexão da IA via API', [
                //     'error' => $e->getMessage(),
                //     'trace' => $e->getTraceAsString(),
                //     'user_id' => auth()->id(),
                //     'request_data' => $request->all()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar configurações ativas
     */
    public function listarConfiguracoes(): JsonResponse
    {
        try {
            $configuracoes = \App\Models\AIConfiguration::active()
                ->byPriority()
                ->get(['id', 'name', 'provider', 'model', 'priority', 'daily_tokens_used', 'daily_token_limit'])
                ->map(function ($config) {
                    $config->makeVisible(['daily_tokens_used', 'daily_token_limit']);
                    $config->resetDailyCounterIfNeeded();
                    return [
                        'id' => $config->id,
                        'name' => $config->name,
                        'provider' => $config->provider,
                        'model' => $config->model,
                        'priority' => $config->priority,
                        'can_be_used' => $config->canBeUsed(),
                        'remaining_tokens' => $config->remaining_tokens,
                        'usage_percentage' => $config->daily_usage_percentage,
                        'is_healthy' => $config->isHealthy()
                    ];
                });

            return response()->json([
                'success' => true,
                'configurations' => $configuracoes
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao listar configurações de IA', [
                //     'error' => $e->getMessage(),
                //     'trace' => $e->getTraceAsString(),
                //     'user_id' => auth()->id()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar configurações: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter estatísticas das configurações
     */
    public function estatisticas(): JsonResponse
    {
        try {
            $configuracoes = \App\Models\AIConfiguration::active()->get();
            
            $stats = [
                'total_configuracoes' => $configuracoes->count(),
                'configuracoes_saudaveis' => $configuracoes->filter->isHealthy()->count(),
                'configuracoes_disponiveis' => $configuracoes->filter->canBeUsed()->count(),
                'uso_total_hoje' => $configuracoes->sum('daily_tokens_used'),
                'configuracoes' => $configuracoes->map(function ($config) {
                    return [
                        'id' => $config->id,
                        'name' => $config->name,
                        'provider' => $config->provider,
                        'is_healthy' => $config->isHealthy(),
                        'can_be_used' => $config->canBeUsed(),
                        'usage_percentage' => $config->daily_usage_percentage
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao obter estatísticas de IA', [
                //     'error' => $e->getMessage(),
                //     'user_id' => auth()->id()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter estatísticas: ' . $e->getMessage()
            ], 500);
        }
    }
}