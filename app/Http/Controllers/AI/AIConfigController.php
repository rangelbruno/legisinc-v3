<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Services\AI\AIProviderService;

class AIConfigController extends Controller
{
    protected $aiProviderService;

    public function __construct(AIProviderService $aiProviderService)
    {
        $this->aiProviderService = $aiProviderService;
    }

    /**
     * Display AI configuration dashboard
     */
    public function index(): View
    {
        // Get all AI providers
        $providers = DB::table('ai_providers')
            ->where('is_enabled', true)
            ->orderBy('name')
            ->get()
            ->map(function ($provider) {
                $provider->supported_models = json_decode($provider->supported_models, true) ?? [];
                $provider->config_template = json_decode($provider->config_template, true) ?? [];
                
                // Check API status for active providers using real validation
                if ($provider->is_active) {
                    $statusCheck = $this->aiProviderService->validateProviderConnection($provider->id);
                    $provider->api_status = $statusCheck['status'];
                    $provider->api_error_message = $statusCheck['message'] ?? null;
                    $provider->last_checked = Carbon::now();
                }
                
                return $provider;
            });

        // Get active provider
        $activeProvider = DB::table('ai_providers')
            ->where('is_active', true)
            ->first();

        // Get real token usage stats for last 30 days
        $tokenStats = $this->aiProviderService->getRealUsageStatistics();

        // Get real chart data
        $chartData = $this->aiProviderService->getRealChartData();

        // Extract values from tokenStats for view
        $totalTokens = $tokenStats['totalTokens'] ?? 0;
        $totalCost = $tokenStats['totalCost'] ?? 0;

        return view('modules.parametros.ia-config', compact(
            'providers', 
            'activeProvider', 
            'tokenStats',
            'chartData',
            'totalTokens',
            'totalCost'
        ));
    }

    /**
     * Save provider configuration
     */
    public function saveProviderConfig(Request $request, int $providerId): JsonResponse
    {
        try {
            $request->validate([
                'default_model' => 'required|string',
                'api_key' => 'required_if:provider,openai,anthropic,google|string'
            ]);

            DB::beginTransaction();

            // Get provider
            $provider = DB::table('ai_providers')->where('id', $providerId)->first();
            
            if (!$provider) {
                return response()->json(['success' => false, 'message' => 'Provedor não encontrado']);
            }

            // Update default model
            DB::table('ai_providers')
                ->where('id', $providerId)
                ->update([
                    'default_model' => $request->default_model,
                    'updated_at' => Carbon::now()
                ]);

            // Clear existing configs for this provider
            DB::table('ai_provider_configs')
                ->where('provider_id', $providerId)
                ->delete();

            // Save new configs
            $configs = $request->except(['_token', 'default_model']);
            foreach ($configs as $key => $value) {
                if (!empty($value)) {
                    $isEncrypted = in_array($key, ['api_key', 'secret_key', 'password']);
                    
                    DB::table('ai_provider_configs')->insert([
                        'provider_id' => $providerId,
                        'config_key' => $key,
                        'config_value' => $isEncrypted ? Crypt::encrypt($value) : $value,
                        'is_encrypted' => $isEncrypted,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Configuração salva com sucesso!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false, 
                'message' => 'Erro ao salvar configuração: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Activate a provider
     */
    public function activateProvider(int $providerId): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Deactivate all providers
            DB::table('ai_providers')
                ->update(['is_active' => false, 'updated_at' => Carbon::now()]);

            // Activate the selected provider
            $updated = DB::table('ai_providers')
                ->where('id', $providerId)
                ->update(['is_active' => true, 'updated_at' => Carbon::now()]);

            if (!$updated) {
                throw new \Exception('Provedor não encontrado');
            }

            DB::commit();

            // Get provider name for response
            $provider = DB::table('ai_providers')->where('id', $providerId)->first();

            return response()->json([
                'success' => true,
                'message' => "Provedor {$provider->label} ativado com sucesso!",
                'active_provider' => $provider->label
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao ativar provedor: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get token usage statistics
     */
    private function getTokenUsageStats(): array
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(30);

        $stats = DB::table('ai_token_usage')
            ->whereBetween('used_at', [$startDate, $endDate])
            ->selectRaw('
                SUM(total_tokens) as total_tokens,
                SUM(prompt_tokens) as prompt_tokens,
                SUM(completion_tokens) as completion_tokens,
                SUM(cost_usd) as total_cost,
                COUNT(*) as total_requests
            ')
            ->first();

        // If no real data, generate realistic test stats
        if (!$stats || $stats->total_tokens == 0) {
            return $this->generateTestStats();
        }

        return [
            'totalTokens' => $stats->total_tokens ?? 0,
            'promptTokens' => $stats->prompt_tokens ?? 0,
            'completionTokens' => $stats->completion_tokens ?? 0,
            'totalCost' => $stats->total_cost ?? 0,
            'totalRequests' => $stats->total_requests ?? 0
        ];
    }

    /**
     * Generate realistic test statistics
     */
    private function generateTestStats(): array
    {
        // Simulate 30 days of realistic usage
        $totalTokens = mt_rand(180000, 250000); // ~6k-8k per day average
        $promptTokens = (int)($totalTokens * 0.4); // 40% prompts
        $completionTokens = $totalTokens - $promptTokens; // 60% completions
        
        // Cost calculation (approximate rates)
        // GPT-4: ~$0.03/1k tokens input, ~$0.06/1k tokens output
        $promptCost = ($promptTokens / 1000) * 0.03;
        $completionCost = ($completionTokens / 1000) * 0.06;
        $totalCost = $promptCost + $completionCost;
        
        $totalRequests = mt_rand(800, 1500); // Realistic request count
        
        return [
            'totalTokens' => $totalTokens,
            'promptTokens' => $promptTokens,
            'completionTokens' => $completionTokens,
            'totalCost' => round($totalCost, 3),
            'totalRequests' => $totalRequests
        ];
    }

    /**
     * Get chart data for token usage over time
     */
    private function getChartData(): array
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(30);

        $data = DB::table('ai_token_usage')
            ->whereBetween('used_at', [$startDate, $endDate])
            ->selectRaw('DATE(used_at) as date, SUM(total_tokens) as total_tokens')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // If no real data, generate realistic test data
        if ($data->isEmpty()) {
            return $this->generateTestChartData($startDate, $endDate);
        }

        // Fill missing dates with 0
        $chartData = [];
        $current = $startDate->copy();
        
        while ($current->lte($endDate)) {
            $dateStr = $current->format('Y-m-d');
            $existing = $data->firstWhere('date', $dateStr);
            
            $chartData[] = [
                'date' => $current->format('d/m'),
                'total_tokens' => $existing ? (int)$existing->total_tokens : 0
            ];
            
            $current->addDay();
        }

        return $chartData;
    }

    /**
     * Generate realistic test data for chart
     */
    private function generateTestChartData(Carbon $startDate, Carbon $endDate): array
    {
        $chartData = [];
        $current = $startDate->copy();
        $baseUsage = 5000; // Base daily usage
        
        while ($current->lte($endDate)) {
            // Create realistic patterns
            $dayOfWeek = $current->dayOfWeek;
            $dayOfMonth = $current->day;
            
            // Higher usage on weekdays, lower on weekends
            $weekdayMultiplier = ($dayOfWeek >= 1 && $dayOfWeek <= 5) ? 1.2 : 0.6;
            
            // Gradual increase over time (simulating growth)
            $growthMultiplier = 1 + ($current->diffInDays($startDate) / 100);
            
            // Add some randomness
            $randomMultiplier = mt_rand(70, 130) / 100;
            
            // Special spikes for certain days
            $spikeMultiplier = 1;
            if ($dayOfMonth % 7 === 0) { // Weekly reports
                $spikeMultiplier = 1.8;
            } elseif ($dayOfMonth % 15 === 0) { // Bi-weekly heavy usage
                $spikeMultiplier = 2.2;
            }
            
            $totalTokens = (int)($baseUsage * $weekdayMultiplier * $growthMultiplier * $randomMultiplier * $spikeMultiplier);
            
            $chartData[] = [
                'date' => $current->format('d/m'),
                'total_tokens' => $totalTokens
            ];
            
            $current->addDay();
        }

        return $chartData;
    }

    /**
     * Record token usage
     */
    public function recordTokenUsage(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'provider_id' => 'required|integer',
                'model' => 'required|string',
                'prompt_tokens' => 'required|integer|min:0',
                'completion_tokens' => 'required|integer|min:0',
                'operation_type' => 'nullable|string',
                'cost_usd' => 'nullable|numeric|min:0'
            ]);

            DB::table('ai_token_usage')->insert([
                'provider_id' => $request->provider_id,
                'model' => $request->model,
                'prompt_tokens' => $request->prompt_tokens,
                'completion_tokens' => $request->completion_tokens,
                'total_tokens' => $request->prompt_tokens + $request->completion_tokens,
                'operation_type' => $request->operation_type,
                'user_id' => auth()->id(),
                'cost_usd' => $request->cost_usd ?? 0,
                'used_at' => Carbon::now(),
                'metadata' => json_encode($request->metadata ?? [])
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate sample usage data for testing
     */
    public function generateSampleData(int $providerId): JsonResponse
    {
        try {
            $success = $this->aiProviderService->generateSampleUsageData($providerId);
            
            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Dados de demonstração gerados com sucesso! A página será recarregada para mostrar os novos dados.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao gerar dados de demonstração'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get provider configuration
     */
    public function getProviderConfig(int $providerId): JsonResponse
    {
        $provider = DB::table('ai_providers')->where('id', $providerId)->first();
        
        if (!$provider) {
            return response()->json(['success' => false, 'message' => 'Provedor não encontrado']);
        }

        $configs = DB::table('ai_provider_configs')
            ->where('provider_id', $providerId)
            ->get()
            ->mapWithKeys(function ($config) {
                $value = $config->is_encrypted ? Crypt::decrypt($config->config_value) : $config->config_value;
                return [$config->config_key => $value];
            });

        $provider->supported_models = json_decode($provider->supported_models, true) ?? [];
        $provider->config_template = json_decode($provider->config_template, true) ?? [];

        return response()->json([
            'success' => true,
            'provider' => $provider,
            'configs' => $configs
        ]);
    }

    /**
     * Test provider connection (public endpoint)
     */
    public function testProviderConnection(int $providerId): JsonResponse
    {
        try {
            $result = $this->aiProviderService->validateProviderConnection($providerId);
            
            if ($result['status'] === 'success') {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'details' => $result['details'] ?? [
                        'status' => 'online',
                        'provider_ready' => true
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'error_details' => $result['message'],
                    'details' => [
                        'status' => 'offline',
                        'provider_ready' => false
                    ]
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno no teste',
                'error_details' => 'Erro interno: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Check provider API status
     */
    private function checkProviderApiStatus(int $providerId): array
    {
        try {
            $provider = DB::table('ai_providers')->where('id', $providerId)->first();
            if (!$provider) {
                return ['status' => 'error', 'error_message' => 'Provedor não encontrado'];
            }

            // Get provider configs
            $configs = DB::table('ai_provider_configs')
                ->where('provider_id', $providerId)
                ->get()
                ->mapWithKeys(function ($config) {
                    $value = $config->is_encrypted ? Crypt::decrypt($config->config_value) : $config->config_value;
                    return [$config->config_key => $value];
                });

            // Simulate API check - in real implementation, this would make actual API calls
            $apiKey = $configs['api_key'] ?? null;
            
            if (empty($apiKey)) {
                return [
                    'status' => 'error', 
                    'error_message' => 'Chave de API não configurada. Configure a chave de API para ativar a comunicação.'
                ];
            }

            // Simulate different scenarios with weighted probabilities
            // 70% success, 30% various errors for realistic testing
            $scenarios = [
                // Success scenarios (70% probability)
                ['status' => 'success', 'weight' => 7],
                ['status' => 'success', 'weight' => 7],
                ['status' => 'success', 'weight' => 7],
                ['status' => 'success', 'weight' => 7],
                ['status' => 'success', 'weight' => 7],
                ['status' => 'success', 'weight' => 7],
                ['status' => 'success', 'weight' => 7],
                
                // Error scenarios (30% probability)
                ['status' => 'error', 'error_message' => 'Chave de API inválida. Verifique se a chave está correta e ativa.', 'weight' => 1],
                ['status' => 'error', 'error_message' => 'Cota de API excedida. Verifique seu limite de uso no painel do provedor.', 'weight' => 1],
                ['status' => 'error', 'error_message' => 'Modelo "' . $provider->default_model . '" temporariamente indisponível. Tente novamente em alguns minutos.', 'weight' => 1]
            ];

            // Select random scenario based on weights
            $totalWeight = array_sum(array_column($scenarios, 'weight'));
            $randomNumber = mt_rand(1, $totalWeight);
            $currentWeight = 0;
            
            $randomScenario = ['status' => 'success']; // fallback
            foreach ($scenarios as $scenario) {
                $currentWeight += $scenario['weight'];
                if ($randomNumber <= $currentWeight) {
                    $randomScenario = $scenario;
                    break;
                }
            }
            
            return $randomScenario;

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error_message' => 'Erro interno ao verificar status da API: ' . $e->getMessage()
            ];
        }
    }
}