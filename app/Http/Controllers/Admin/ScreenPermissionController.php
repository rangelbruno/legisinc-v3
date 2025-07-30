<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DynamicPermissionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class ScreenPermissionController extends Controller
{
    private DynamicPermissionService $permissionService;
    
    public function __construct(DynamicPermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Exibir tela principal de gerenciamento de permissões
     */
    public function index(): View
    {
        try {
            Log::info('ScreenPermissionController::index called');
            
            $data = $this->permissionService->getPermissionStructure();
            
            Log::info('Permission structure loaded:');
            Log::info('Modules count: ' . $data['modules']->count());
            Log::info('Roles count: ' . $data['roles']->count());
            Log::info('Current permissions: ' . json_encode($data['current']));
            
            // Adicionar informações sobre configurações padrão
            $roleStatuses = [];
            foreach ($data['roles'] as $role) {
                $roleStatuses[$role['name']] = $this->permissionService->getRoleConfigurationStatus($role['name']);
            }

            return view('admin.screen-permissions.dynamic', [
                'modules' => $data['modules'],
                'roles' => $data['roles'],
                'defaults' => $data['defaults'],
                'current' => $data['current'],
                'statistics' => $data['statistics'],
                'roleStatuses' => $roleStatuses
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao carregar permissões: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Fallback para dados vazios
            return view('admin.screen-permissions.dynamic', [
                'modules' => collect(),
                'roles' => collect(),
                'defaults' => [],
                'current' => [],
                'statistics' => [
                    'total_routes' => 0,
                    'total_permissions' => 0,
                    'active_permissions' => 0,
                    'role_count' => 0,
                    'coverage_percentage' => 0
                ]
            ]);
        }
    }

    /**
     * Salvar permissões de um role
     */
    public function saveRolePermissions(Request $request): JsonResponse
    {
        $startTime = microtime(true);
        Log::info('ScreenPermissionController::saveRolePermissions iniciado');
        
        $request->validate([
            'role' => 'required|string',
            'permissions' => 'required|array'
        ]);

        try {
            Log::info('Dados recebidos:', [
                'role' => $request->input('role'),
                'permissions_count' => count($request->input('permissions'))
            ]);
            
            $success = $this->permissionService->saveRolePermissions(
                $request->input('role'),
                $request->input('permissions')
            );

            $endTime = microtime(true);
            $duration = ($endTime - $startTime) * 1000; // em millisegundos
            
            Log::info('Processo de salvamento concluído em ' . round($duration, 2) . 'ms');

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Permissões salvas com sucesso! As telas selecionadas agora aparecerão no menu dos usuários.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao salvar permissões. Verifique os logs.'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Erro ao salvar permissões: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Aplicar permissões padrão para um role
     */
    public function applyDefaults(Request $request): JsonResponse
    {
        $request->validate([
            'role' => 'required|string'
        ]);

        try {
            $success = $this->permissionService->applyDefaultPermissions(
                $request->input('role')
            );

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Permissões padrão aplicadas com sucesso!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Role não encontrado ou erro ao aplicar permissões padrão.'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Erro ao aplicar permissões padrão: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Inicializar sistema com permissões padrão para todos os roles
     */
    public function initializeSystem(): JsonResponse
    {
        try {
            $results = $this->permissionService->initializeDefaultPermissions();
            
            $successful = array_filter($results);
            $failed = array_diff_key($results, $successful);
            
            $message = count($successful) . ' roles configurados com sucesso.';
            if (count($failed) > 0) {
                $message .= ' ' . count($failed) . ' roles falharam: ' . implode(', ', array_keys($failed));
            }

            return response()->json([
                'success' => count($successful) > 0,
                'message' => $message,
                'results' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao inicializar sistema: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter permissões de um role específico
     */
    public function getRolePermissions(string $role): JsonResponse
    {
        try {
            Log::info('ScreenPermissionController::getRolePermissions called for role: ' . $role);
            
            $permissions = $this->permissionService->getRolePermissions($role);
            
            Log::info('Permissions retrieved: ' . $permissions->count() . ' items');
            
            // Retornar apenas as rotas que têm can_access = true como array simples
            $activeRoutes = [];
            foreach ($permissions as $route => $permission) {
                if ($permission->can_access) {
                    $activeRoutes[] = $permission->screen_route;
                }
            }
            
            Log::info('Active routes: ' . json_encode($activeRoutes));
            
            return response()->json([
                'success' => true,
                'permissions' => $activeRoutes
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao buscar permissões do role: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar permissões: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Testar permissões de um usuário
     */
    public function testUserPermissions(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id'
        ]);

        try {
            $user = \App\Models\User::findOrFail($request->input('user_id'));
            $menu = $this->permissionService->getUserMenu($user);
            
            return response()->json([
                'success' => true,
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames()
                ],
                'menu' => $menu
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao testar permissões: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao testar permissões: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Aplicar configuração padrão para um role específico
     */
    public function applyDefault(Request $request): JsonResponse
    {
        $request->validate([
            'role' => 'required|string'
        ]);

        try {
            $roleName = $request->input('role');
            
            Log::info('Aplicando configuração padrão para role: ' . $roleName);
            
            // Aplicar permissões padrão
            $success = $this->permissionService->applyDefaultPermissions($roleName);
            
            if ($success) {
                // Buscar as permissões aplicadas para retornar
                $permissions = $this->permissionService->getRolePermissions($roleName);
                $permissionArray = [];
                
                foreach ($permissions as $permission) {
                    if ($permission->can_access) {
                        $permissionArray[$permission->screen_route] = true;
                    }
                }
                
                Log::info('Configuração padrão aplicada com sucesso', [
                    'role' => $roleName,
                    'permissions_count' => count($permissionArray)
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Configuração padrão aplicada com sucesso!',
                    'permissions' => $permissionArray,
                    'is_default' => true
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao aplicar configuração padrão'
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Erro ao aplicar configuração padrão: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao aplicar configuração padrão: ' . $e->getMessage()
            ], 500);
        }
    }
}