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
            // Se dados foram enviados via formulário, usar esses dados temporariamente
            $configData = null;
            if ($request->has(['ai_provider', 'ai_api_key', 'ai_model'])) {
                $configData = [
                    'provider' => $request->input('ai_provider'),
                    'api_key' => $request->input('ai_api_key'),
                    'model' => $request->input('ai_model'),
                    'max_tokens' => (int) ($request->input('ai_max_tokens') ?: 2000),
                    'temperature' => (float) ($request->input('ai_temperature') ?: 0.7),
                    'custom_prompt' => $request->input('ai_custom_prompt')
                ];
            }

            $resultado = $this->aiService->testarConexao($configData);

            return response()->json($resultado);

        } catch (\Exception $e) {
            \Log::error('Erro no teste de conexão da IA via API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor: ' . $e->getMessage()
            ], 500);
        }
    }
}