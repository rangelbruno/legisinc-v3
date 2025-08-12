<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AIConfiguration;
use App\Services\AI\AITextGenerationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AIConfigurationController extends Controller
{
    protected AITextGenerationService $aiService;

    public function __construct(AITextGenerationService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Display a listing of AI configurations.
     */
    public function index(): View
    {
        // Log::info('Acessando index das configurações de IA', ['user' => auth()->id()]);
        
        // Temporariamente usar coleção vazia se houver problemas com o banco
        try {
            $configurations = AIConfiguration::orderBy('priority')
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            // Log::warning('Erro ao buscar configurações, usando coleção vazia', ['error' => $e->getMessage()]);
            $configurations = collect();
        }

        $providers = AIConfiguration::getAvailableProviders();

        return view('admin.ai-configurations.index', compact('configurations', 'providers'));
    }

    /**
     * Show the form for creating a new AI configuration.
     */
    public function create(): View
    {
        $providers = AIConfiguration::getAvailableProviders();
        
        return view('admin.ai-configurations.create', compact('providers'));
    }

    /**
     * Store a newly created AI configuration.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            // Primeiro verificar se a tabela existe
            if (!DB::getSchemaBuilder()->hasTable('ai_configurations')) {
                return back()
                    ->withErrors(['general' => 'Tabela ai_configurations não encontrada. Execute as migrations primeiro.'])
                    ->withInput();
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:ai_configurations',
                'provider' => 'required|string|in:openai,anthropic,google,local',
                'api_key' => 'nullable|string|max:1000',
                'model' => 'required|string|max:100',
                'base_url' => 'nullable|url|max:255',
                'max_tokens' => 'required|integer|min:100|max:32000',
                'temperature' => 'required|numeric|min:0|max:2',
                'custom_prompt' => 'nullable|string',
                'priority' => 'required|integer|min:1|max:100',
                'is_active' => 'boolean',
                'daily_token_limit' => 'nullable|integer|min:100',
                'cost_per_1k_tokens' => 'nullable|numeric|min:0',
                'additional_parameters' => 'nullable|json',
            ]);

            // Validação específica por provedor
            if (in_array($validated['provider'], ['openai', 'anthropic', 'google']) && empty($validated['api_key'])) {
                return back()
                    ->withErrors(['api_key' => 'API Key é obrigatória para o provedor ' . $validated['provider']])
                    ->withInput();
            }

            DB::beginTransaction();

            // Log::info('Tentando criar configuração de IA', [
                //     'name' => $validated['name'],
                //     'provider' => $validated['provider'],
                //     'user' => auth()->id()
            // ]);

            $configuration = AIConfiguration::create($validated);

            // Log::info('Configuração de IA criada com sucesso', [
                //     'id' => $configuration->id,
                //     'name' => $configuration->name
            // ]);

            // Testar a configuração se solicitado
            if ($request->has('test_connection')) {
                // Log::info('Testando configuração após criação');
                $testResult = $this->aiService->testarConexao(null, $configuration->id);
                
                if (!$testResult['success']) {
                    DB::rollback();
                    // Log::warning('Teste falhou após criação', ['result' => $testResult]);
                    return back()
                        ->withErrors(['test' => 'Configuração criada mas falhou no teste: ' . $testResult['message']])
                        ->withInput();
                }
                
                // Log::info('Teste passou com sucesso após criação');
            }

            DB::commit();

            return redirect()
                ->route('admin.ai-configurations.index')
                ->with('success', 'Configuração de IA criada com sucesso!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            DB::rollback();
            // Log::error('Erro ao criar configuração de IA', [
                //     'error' => $e->getMessage(),
                //     'trace' => $e->getTraceAsString(),
                //     'data' => $request->all(),
                //     'user' => auth()->id()
            // ]);

            $errorMessage = 'Erro ao criar configuração: ' . $e->getMessage();
            
            // Adicionar informações extras para debug se necessário
            if (config('app.debug')) {
                $errorMessage .= "\n\nDetalhes técnicos: " . $e->getTraceAsString();
            }

            return back()
                ->withErrors(['general' => $errorMessage])
                ->withInput();
        }
    }

    /**
     * Display the specified AI configuration.
     */
    public function show(AIConfiguration $aiConfiguration): View
    {
        return view('admin.ai-configurations.show', compact('aiConfiguration'));
    }

    /**
     * Show the form for editing the specified AI configuration.
     */
    public function edit(AIConfiguration $aiConfiguration): View
    {
        $providers = AIConfiguration::getAvailableProviders();
        
        return view('admin.ai-configurations.edit', compact('aiConfiguration', 'providers'));
    }

    /**
     * Update the specified AI configuration.
     */
    public function update(Request $request, AIConfiguration $aiConfiguration): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:ai_configurations,name,' . $aiConfiguration->id,
            'provider' => 'required|string|in:openai,anthropic,google,local',
            'api_key' => 'nullable|string|max:1000',
            'model' => 'required|string|max:100',
            'base_url' => 'nullable|url|max:255',
            'max_tokens' => 'required|integer|min:100|max:32000',
            'temperature' => 'required|numeric|min:0|max:2',
            'custom_prompt' => 'nullable|string',
            'priority' => 'required|integer|min:1|max:100',
            'is_active' => 'boolean',
            'daily_token_limit' => 'nullable|integer|min:100',
            'cost_per_1k_tokens' => 'nullable|numeric|min:0',
            'additional_parameters' => 'nullable|json',
        ]);

        try {
            DB::beginTransaction();

            $aiConfiguration->update($validated);

            // Testar a configuração se solicitado
            if ($request->has('test_connection')) {
                $testResult = $this->aiService->testarConexao(null, $aiConfiguration->id);
                
                if (!$testResult['success']) {
                    return back()
                        ->with('warning', 'Configuração atualizada, mas falhou no teste: ' . $testResult['message']);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.ai-configurations.index')
                ->with('success', 'Configuração de IA atualizada com sucesso!');

        } catch (\Exception $e) {
            DB::rollback();
            // Log::error('Erro ao atualizar configuração de IA', [
                //     'id' => $aiConfiguration->id,
                //     'error' => $e->getMessage(),
                //     'data' => $validated
            // ]);

            return back()
                ->withErrors(['general' => 'Erro ao atualizar configuração: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified AI configuration.
     */
    public function destroy(AIConfiguration $aiConfiguration): RedirectResponse
    {
        try {
            $name = $aiConfiguration->name;
            $aiConfiguration->delete();

            return redirect()
                ->route('admin.ai-configurations.index')
                ->with('success', "Configuração '$name' removida com sucesso!");

        } catch (\Exception $e) {
            // Log::error('Erro ao remover configuração de IA', [
                //     'id' => $aiConfiguration->id,
                //     'error' => $e->getMessage()
            // ]);

            return back()
                ->withErrors(['general' => 'Erro ao remover configuração: ' . $e->getMessage()]);
        }
    }

    /**
     * Test connection for a specific configuration.
     */
    public function testConnection(Request $request, AIConfiguration $aiConfiguration): JsonResponse
    {
        try {
            $result = $this->aiService->testarConexao(null, $aiConfiguration->id);
            
            return response()->json($result);

        } catch (\Exception $e) {
            // Log::error('Erro ao testar conexão de IA', [
                //     'id' => $aiConfiguration->id,
                //     'error' => $e->getMessage()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro no teste de conexão: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test connection with provided data (for forms).
     */
    public function testConnectionData(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'provider' => 'required|string|in:openai,anthropic,google,local',
                'api_key' => 'nullable|string|max:1000',
                'model' => 'required|string|max:100',
                'base_url' => 'nullable|url|max:255',
                'max_tokens' => 'required|integer|min:100|max:32000',
                'temperature' => 'required|numeric|min:0|max:2',
            ]);

            // Validação específica por provedor
            if (in_array($validated['provider'], ['openai', 'anthropic', 'google']) && empty($validated['api_key'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'API Key é obrigatória para o provedor ' . $validated['provider']
                ], 422);
            }

            // Teste da configuração
            $result = $this->aiService->testarConexao($validated);
            
            return response()->json($result);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos: ' . implode(', ', array_flatten($e->errors()))
            ], 422);
            
        } catch (\Exception $e) {
            // Log::error('Erro ao testar dados de conexão de IA', [
                //     'error' => $e->getMessage(),
                //     'trace' => $e->getTraceAsString(),
                //     'data' => $request->all()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro no teste de conexão: ' . $e->getMessage(),
                'details' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Test all active configurations.
     */
    public function testAllConnections(): JsonResponse
    {
        try {
            $results = $this->aiService->testarTodasConfiguracoes();
            
            return response()->json([
                'success' => true,
                'results' => $results
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao testar todas as configurações de IA', [
                //     'error' => $e->getMessage()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao testar configurações: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle active status of configuration.
     */
    public function toggleActive(AIConfiguration $aiConfiguration): JsonResponse
    {
        try {
            $aiConfiguration->update(['is_active' => !$aiConfiguration->is_active]);
            
            return response()->json([
                'success' => true,
                'is_active' => $aiConfiguration->is_active,
                'message' => $aiConfiguration->is_active 
                    ? 'Configuração ativada' 
                    : 'Configuração desativada'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao alterar status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset daily usage counters for a configuration.
     */
    public function resetDailyUsage(AIConfiguration $aiConfiguration): JsonResponse
    {
        try {
            $aiConfiguration->update([
                'daily_tokens_used' => 0,
                'last_reset_date' => now()->toDateString()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Contador diário resetado com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao resetar contador: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get provider models via AJAX.
     */
    public function getProviderModels(Request $request): JsonResponse
    {
        $provider = $request->input('provider');
        $providers = AIConfiguration::getAvailableProviders();
        
        if (!isset($providers[$provider])) {
            return response()->json([
                'success' => false,
                'message' => 'Provedor não encontrado'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'models' => $providers[$provider]['models'],
            'requires_api_key' => $providers[$provider]['requires_api_key'],
            'default_base_url' => $providers[$provider]['default_base_url'],
            'cost_per_1k_tokens' => $providers[$provider]['cost_per_1k_tokens']
        ]);
    }

    /**
     * Reorder configurations priorities.
     */
    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:ai_configurations,id'
        ]);

        try {
            DB::transaction(function () use ($validated) {
                foreach ($validated['ids'] as $index => $id) {
                    AIConfiguration::where('id', $id)->update(['priority' => $index + 1]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Ordem das configurações atualizada'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao reordenar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get usage statistics.
     */
    public function getUsageStats(): JsonResponse
    {
        try {
            $configurations = AIConfiguration::active()->get();
            
            $stats = $configurations->map(function ($config) {
                $config->resetDailyCounterIfNeeded();
                
                return [
                    'id' => $config->id,
                    'name' => $config->name,
                    'provider' => $config->provider,
                    'model' => $config->model,
                    'daily_tokens_used' => $config->daily_tokens_used,
                    'daily_token_limit' => $config->daily_token_limit,
                    'remaining_tokens' => $config->remaining_tokens,
                    'usage_percentage' => $config->daily_usage_percentage,
                    'is_healthy' => $config->isHealthy(),
                    'last_tested_at' => $config->last_tested_at?->format('d/m/Y H:i'),
                ];
            });

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter estatísticas: ' . $e->getMessage()
            ], 500);
        }
    }
}