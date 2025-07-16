<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PermissionManagementService;
use App\Services\PermissionCacheService;
use App\Enums\UserRole;
use App\Enums\SystemModule;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class ScreenPermissionController extends Controller
{
    public function __construct(
        private PermissionManagementService $permissionService,
        private PermissionCacheService $cacheService
    ) {
        // Middleware será aplicado nas rotas, não no construtor
    }

    /**
     * Exibir tela principal de gerenciamento de permissões
     */
    public function index(): View
    {
        $roles = UserRole::getAllCases();
        $modules = SystemModule::getAllWithRoutes();
        
        try {
            $permissionMatrix = $this->permissionService->getPermissionMatrix();
            $statistics = $this->permissionService->getPermissionStatistics();
            $cacheStats = $this->cacheService->getCacheStatistics();
        } catch (\Exception $e) {
            Log::error('Erro ao carregar dados da tela de permissões', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Dados padrão em caso de erro
            $permissionMatrix = [];
            $statistics = [
                'total_permissions' => 0,
                'active_permissions' => 0,
                'coverage_percentage' => 0
            ];
            $cacheStats = [
                'hits' => 0,
                'misses' => 0,
                'hit_ratio' => 0
            ];
        }
        
        return view('admin.screen-permissions.index', compact(
            'roles',
            'modules',
            'permissionMatrix',
            'statistics',
            'cacheStats'
        ));
    }

    /**
     * Atualizar permissões de um perfil
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'role' => 'required|string',
            'permissions' => 'required|array',
            'permissions.*.screen_route' => 'required|string',
            'permissions.*.can_access' => 'boolean',
            'permissions.*.can_create' => 'boolean',
            'permissions.*.can_edit' => 'boolean',
            'permissions.*.can_delete' => 'boolean',
        ]);

        try {
            $this->permissionService->updateRolePermissions(
                $request->input('role'),
                $request->input('permissions')
            );

            // Limpar cache após alterações
            $this->cacheService->clearAllPermissionCaches();

            return response()->json([
                'success' => true,
                'message' => 'Permissões atualizadas com sucesso!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar permissões: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter permissões de um role específico
     */
    public function getRolePermissions(Request $request, string $role): JsonResponse
    {
        try {
            $permissions = $this->permissionService->getRolePermissions($role);
            
            return response()->json([
                'success' => true,
                'permissions' => $permissions->toArray()
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao carregar permissões', [
                'role' => $role,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar permissões: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resetar permissões de um perfil para o padrão
     */
    public function reset(Request $request): JsonResponse
    {
        $request->validate(['role' => 'required|string']);
        
        try {
            $role = UserRole::from($request->input('role'));
            $this->permissionService->resetRoleToDefaults($role);
            $this->cacheService->clearAllPermissionCaches();
            
            return response()->json([
                'success' => true,
                'message' => 'Permissões resetadas para o padrão com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao resetar permissões: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sincronizar permissões com rotas disponíveis
     */
    public function sync(): JsonResponse
    {
        try {
            $result = $this->permissionService->syncPermissionsWithRoutes();
            $this->cacheService->clearAllPermissionCaches();
            
            return response()->json([
                'success' => true,
                'message' => 'Sincronização concluída com sucesso!',
                'created' => $result['created'],
                'updated' => $result['updated']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro na sincronização: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportar permissões para backup
     */
    public function export(): JsonResponse
    {
        try {
            $backup = $this->permissionService->exportPermissions();
            
            return response()->json([
                'success' => true,
                'data' => $backup,
                'filename' => 'permissions_backup_' . now()->format('Y_m_d_H_i_s') . '.json'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao exportar permissões: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Importar permissões de backup
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'backup_data' => 'required|array'
        ]);

        try {
            $this->permissionService->importPermissions($request->input('backup_data'));
            $this->cacheService->clearAllPermissionCaches();
            
            return response()->json([
                'success' => true,
                'message' => 'Permissões importadas com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao importar permissões: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter estatísticas do cache
     */
    public function cacheStats(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'stats' => $this->cacheService->getCacheStatistics()
        ]);
    }

    /**
     * Limpar cache de permissões
     */
    public function clearCache(): JsonResponse
    {
        try {
            $this->cacheService->clearAllPermissionCaches();
            $this->cacheService->resetCacheStatistics();
            
            return response()->json([
                'success' => true,
                'message' => 'Cache limpo com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao limpar cache: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pré-carregar cache para usuários ativos
     */
    public function warmCache(): JsonResponse
    {
        try {
            $count = $this->cacheService->preloadActiveUserPermissions();
            
            return response()->json([
                'success' => true,
                'message' => "Cache pré-carregado para {$count} usuários ativos!"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao pré-carregar cache: ' . $e->getMessage()
            ], 500);
        }
    }
}
