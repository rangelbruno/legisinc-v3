<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProgressService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProgressApiController extends Controller
{
    protected $progressService;

    public function __construct(ProgressService $progressService)
    {
        $this->progressService = $progressService;
    }

    /**
     * Retorna dados completos do cronograma para a interface Vue.js
     */
    public function schedule(): JsonResponse
    {
        try {
            $scheduleData = $this->progressService->getDetailedScheduleData();
            
            // Adicionar timestamp para cache busting
            $scheduleData['timestamp'] = now()->timestamp;
            $scheduleData['last_update'] = now()->toISOString();
            
            return response()->json($scheduleData);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao carregar dados do cronograma',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna visão geral simplificada do progresso
     */
    public function overview(): JsonResponse
    {
        try {
            $progressData = $this->progressService->getProgressData();
            
            return response()->json([
                'overview' => $progressData['overview'],
                'statistics' => $progressData['statistics'],
                'last_update' => $progressData['lastUpdate'],
                'timestamp' => now()->timestamp
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao carregar visão geral',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualiza progresso de um módulo específico
     */
    public function updateProgress(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'week_id' => 'required|string',
                'module_name' => 'required|string',
                'progress' => 'required|integer|min:0|max:100',
                'status' => 'sometimes|string|in:planned,in_progress,completed'
            ]);

            // Simular atualização de progresso
            // Em uma implementação real, isso seria salvo no banco de dados
            $updateData = [
                'week_id' => $validated['week_id'],
                'module_name' => $validated['module_name'],
                'progress' => $validated['progress'],
                'status' => $validated['status'] ?? 'in_progress',
                'updated_at' => now()->toISOString(),
                'updated_by' => auth()->user()->name ?? 'Sistema'
            ];

            // Log da atualização
            \Log::info('Progress Update', $updateData);

            return response()->json([
                'success' => true,
                'message' => 'Progresso atualizado com sucesso',
                'data' => $updateData,
                'timestamp' => now()->timestamp
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao atualizar progresso',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna estatísticas em tempo real do sistema
     */
    public function realTimeStats(): JsonResponse
    {
        try {
            // Simular dados em tempo real
            $stats = [
                'active_users' => rand(5, 25),
                'current_tasks' => rand(15, 45),
                'completed_today' => rand(3, 12),
                'system_load' => rand(20, 80),
                'last_deployment' => '2025-08-31 19:20:03',
                'uptime' => '15 dias, 8 horas',
                'performance' => [
                    'response_time' => rand(50, 200) . 'ms',
                    'memory_usage' => rand(45, 75) . '%',
                    'cpu_usage' => rand(10, 40) . '%',
                    'database_queries' => rand(100, 500) . '/min'
                ]
            ];

            return response()->json([
                'stats' => $stats,
                'timestamp' => now()->timestamp,
                'generated_at' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao carregar estatísticas',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna dados de timeline dos marcos críticos
     */
    public function timeline(): JsonResponse
    {
        try {
            $scheduleData = $this->progressService->getDetailedScheduleData();
            $timeline = $scheduleData['timeline'];
            
            // Enriquecer com dados calculados
            foreach ($timeline['milestones'] as &$milestone) {
                $milestoneDate = new \DateTime($milestone['date']);
                $now = new \DateTime();
                
                $milestone['days_remaining'] = $milestoneDate > $now ? 
                    $now->diff($milestoneDate)->days : 
                    -($now->diff($milestoneDate)->days);
                    
                $milestone['is_overdue'] = $milestoneDate < $now && $milestone['status'] !== 'completed';
                $milestone['urgency_level'] = $this->calculateUrgencyLevel($milestone['days_remaining']);
            }

            return response()->json([
                'timeline' => $timeline,
                'current_date' => now()->toDateString(),
                'timestamp' => now()->timestamp
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao carregar timeline',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcula nível de urgência baseado nos dias restantes
     */
    private function calculateUrgencyLevel(int $daysRemaining): string
    {
        if ($daysRemaining < 0) {
            return 'overdue';
        } elseif ($daysRemaining <= 3) {
            return 'critical';
        } elseif ($daysRemaining <= 7) {
            return 'high';
        } elseif ($daysRemaining <= 14) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Retorna configurações da interface do usuário
     */
    public function uiSettings(): JsonResponse
    {
        return response()->json([
            'settings' => [
                'auto_update_interval' => 30000, // 30 segundos
                'animation_duration' => 300,
                'theme' => 'default',
                'view_preferences' => [
                    'default_view' => 'cards',
                    'show_animations' => true,
                    'show_progress_bars' => true,
                    'compact_mode' => false
                ],
                'notifications' => [
                    'enabled' => true,
                    'sound' => false,
                    'position' => 'top-right'
                ]
            ],
            'timestamp' => now()->timestamp
        ]);
    }
}