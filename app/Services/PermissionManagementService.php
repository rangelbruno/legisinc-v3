<?php

namespace App\Services;

use App\Models\ScreenPermission;
use App\Enums\UserRole;
use App\Enums\SystemModule;
use App\Enums\PermissionAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class PermissionManagementService
{
    public function __construct(
        private PermissionCacheService $cacheService
    ) {}

    /**
     * Obter matriz completa de permissões por role
     */
    public function getPermissionMatrix(): array
    {
        $matrix = [];
        
        foreach (UserRole::cases() as $role) {
            $permissions = ScreenPermission::where('role_name', $role->value)->get();
            
            $matrix[$role->value] = [
                'role' => $role,
                'label' => $role->getLabel(),
                'description' => $role->getDescription(),
                'level' => $role->getLevel(),
                'color' => $role->getColor(),
                'permissions' => $permissions->keyBy('screen_route'),
                'total_screens' => $this->getTotalScreensCount(),
                'active_permissions' => $permissions->where('can_access', true)->count(),
                'permission_percentage' => $this->calculatePermissionPercentage($permissions),
                'modules_access' => $this->getModuleAccessSummary($permissions),
            ];
        }
        
        return $matrix;
    }

    /**
     * Obter permissões específicas de um role
     */
    public function getRolePermissions(string $roleName): Collection
    {
        return ScreenPermission::where('role_name', $roleName)
            ->get()
            ->groupBy('screen_module');
    }

    /**
     * Atualizar permissões de um role
     */
    public function updateRolePermissions(string $roleName, array $permissions): void
    {
        DB::transaction(function () use ($roleName, $permissions) {
            foreach ($permissions as $permission) {
                $this->updateOrCreatePermission($roleName, $permission);
            }
            
            $this->logPermissionUpdate($roleName, $permissions);
            $this->invalidateRelatedCaches($roleName);
        });
    }

    /**
     * Resetar permissões de um role para os padrões
     */
    public function resetRoleToDefaults(UserRole $role): void
    {
        DB::transaction(function () use ($role) {
            // Remover permissões existentes
            ScreenPermission::where('role_name', $role->value)->delete();
            
            // Aplicar permissões padrão
            $defaultPermissions = $role->getDefaultPermissions();
            
            foreach ($defaultPermissions as $permission) {
                if ($permission === '*') {
                    $this->grantAllPermissions($role);
                    break;
                }
                
                $this->grantSpecificPermission($role, $permission);
            }
            
            $this->logPermissionReset($role);
            $this->invalidateAllCaches();
        });
    }

    /**
     * Conceder todas as permissões a um role
     */
    public function grantAllPermissions(UserRole $role): void
    {
        foreach (SystemModule::cases() as $module) {
            foreach ($module->getRoutes() as $route => $name) {
                ScreenPermission::updateOrCreate(
                    [
                        'role_name' => $role->value,
                        'screen_route' => $route,
                    ],
                    [
                        'screen_name' => $name,
                        'screen_module' => $module->value,
                        'can_access' => true,
                        'can_create' => true,
                        'can_edit' => true,
                        'can_delete' => true,
                    ]
                );
            }
        }
    }

    /**
     * Conceder permissão específica
     */
    public function grantSpecificPermission(UserRole $role, string $permission): void
    {
        [$route, $action] = $this->parsePermissionString($permission);
        
        if (!$route) {
            return;
        }

        $module = $this->findModuleByRoute($route);
        $routeName = $this->findRouteNameByRoute($route);
        
        if (!$module || !$routeName) {
            // Log::warning("Rota não encontrada para permissão: {$permission}");
            return;
        }

        $permissionData = [
            'screen_name' => $module->getRoutes()[$route] ?? $route,
            'screen_module' => $module->value,
            'can_access' => in_array($action, ['view', '*']),
            'can_create' => in_array($action, ['create', '*']),
            'can_edit' => in_array($action, ['edit', '*']),
            'can_delete' => in_array($action, ['delete', '*']),
        ];

        ScreenPermission::updateOrCreate(
            [
                'role_name' => $role->value,
                'screen_route' => $route,
            ],
            $permissionData
        );
    }

    /**
     * Verificar se role pode modificar outro role
     */
    public function canModifyRole(UserRole $currentRole, UserRole $targetRole): bool
    {
        return $currentRole->canAccessRole($targetRole);
    }

    /**
     * Obter estatísticas de permissões
     */
    public function getPermissionStatistics(): array
    {
        $totalPermissions = ScreenPermission::count();
        $activePermissions = ScreenPermission::where('can_access', true)->count();
        $rolesWithPermissions = ScreenPermission::distinct('role_name')->count();
        
        return [
            'total_permissions' => $totalPermissions,
            'active_permissions' => $activePermissions,
            'inactive_permissions' => $totalPermissions - $activePermissions,
            'roles_with_permissions' => $rolesWithPermissions,
            'total_possible_permissions' => $this->getTotalPossiblePermissions(),
            'coverage_percentage' => $this->calculateCoveragePercentage(),
        ];
    }

    /**
     * Sincronizar permissões com rotas disponíveis
     */
    public function syncPermissionsWithRoutes(): array
    {
        $created = 0;
        $updated = 0;
        
        foreach (UserRole::cases() as $role) {
            foreach (SystemModule::cases() as $module) {
                foreach ($module->getRoutes() as $route => $name) {
                    $permission = ScreenPermission::firstOrCreate(
                        [
                            'role_name' => $role->value,
                            'screen_route' => $route,
                        ],
                        [
                            'screen_name' => $name,
                            'screen_module' => $module->value,
                            'can_access' => false,
                            'can_create' => false,
                            'can_edit' => false,
                            'can_delete' => false,
                        ]
                    );
                    
                    if ($permission->wasRecentlyCreated) {
                        $created++;
                    }
                }
            }
        }
        
        // Log::info("Sincronização de permissões concluída", [
            //     'created' => $created,
            //     'updated' => $updated
        // ]);
        
        return compact('created', 'updated');
    }

    /**
     * Exportar permissões para backup
     */
    public function exportPermissions(): array
    {
        return [
            'timestamp' => now()->toDateTimeString(),
            'version' => '1.0',
            'permissions' => ScreenPermission::all()->groupBy('role_name')->toArray(),
            'metadata' => [
                'total_permissions' => ScreenPermission::count(),
                'roles_count' => count(UserRole::cases()),
                'modules_count' => count(SystemModule::cases()),
            ]
        ];
    }

    /**
     * Importar permissões de backup
     */
    public function importPermissions(array $backup): void
    {
        DB::transaction(function () use ($backup) {
            if (!isset($backup['permissions']) || !is_array($backup['permissions'])) {
                throw new \InvalidArgumentException('Formato de backup inválido');
            }
            
            // Limpar permissões existentes
            ScreenPermission::truncate();
            
            // Importar permissões
            foreach ($backup['permissions'] as $roleName => $permissions) {
                foreach ($permissions as $permission) {
                    ScreenPermission::create($permission);
                }
            }
            
            // Log::info('Permissões importadas com sucesso', [
                //     'backup_timestamp' => $backup['timestamp'] ?? 'unknown',
                //     'permissions_imported' => ScreenPermission::count()
            // ]);
            
            $this->invalidateAllCaches();
        });
    }

    // Métodos privados auxiliares

    private function updateOrCreatePermission(string $roleName, array $permission): void
    {
        ScreenPermission::updateOrCreate(
            [
                'role_name' => $roleName,
                'screen_route' => $permission['screen_route'],
            ],
            [
                'screen_name' => $permission['screen_name'] ?? $permission['screen_route'],
                'screen_module' => $permission['screen_module'] ?? $this->inferModuleFromRoute($permission['screen_route']),
                'can_access' => $permission['can_access'] ?? false,
                'can_create' => $permission['can_create'] ?? false,
                'can_edit' => $permission['can_edit'] ?? false,
                'can_delete' => $permission['can_delete'] ?? false,
            ]
        );
    }

    private function calculatePermissionPercentage(Collection $permissions): int
    {
        $totalScreens = $this->getTotalScreensCount();
        $activePermissions = $permissions->where('can_access', true)->count();
        
        return $totalScreens > 0 ? round(($activePermissions / $totalScreens) * 100) : 0;
    }

    private function getModuleAccessSummary(Collection $permissions): array
    {
        $summary = [];
        
        foreach (SystemModule::cases() as $module) {
            $modulePermissions = $permissions->where('screen_module', $module->value);
            $totalModuleScreens = count($module->getRoutes());
            $activeModulePermissions = $modulePermissions->where('can_access', true)->count();
            
            $summary[$module->value] = [
                'total' => $totalModuleScreens,
                'active' => $activeModulePermissions,
                'percentage' => $totalModuleScreens > 0 ? round(($activeModulePermissions / $totalModuleScreens) * 100) : 0,
            ];
        }
        
        return $summary;
    }

    private function getTotalScreensCount(): int
    {
        return collect(SystemModule::cases())
            ->sum(fn($module) => count($module->getRoutes()));
    }

    private function getTotalPossiblePermissions(): int
    {
        return count(UserRole::cases()) * $this->getTotalScreensCount();
    }

    private function calculateCoveragePercentage(): float
    {
        $total = $this->getTotalPossiblePermissions();
        $existing = ScreenPermission::count();
        
        return $total > 0 ? round(($existing / $total) * 100, 2) : 0;
    }

    private function parsePermissionString(string $permission): array
    {
        if (str_contains($permission, '.')) {
            return explode('.', $permission, 2);
        }
        
        return [$permission, 'view'];
    }

    private function findModuleByRoute(string $route): ?SystemModule
    {
        foreach (SystemModule::cases() as $module) {
            if (array_key_exists($route, $module->getRoutes())) {
                return $module;
            }
        }
        
        return null;
    }

    private function findRouteNameByRoute(string $route): ?string
    {
        foreach (SystemModule::cases() as $module) {
            $routes = $module->getRoutes();
            if (isset($routes[$route])) {
                return $routes[$route];
            }
        }
        
        return null;
    }

    private function inferModuleFromRoute(string $route): string
    {
        $parts = explode('.', $route);
        return $parts[0] ?? 'unknown';
    }

    private function logPermissionUpdate(string $roleName, array $permissions): void
    {
        // Log::info('Permissões atualizadas', [
            //     'role' => $roleName,
            //     'admin_user' => auth()->id(),
            //     'permissions_count' => count($permissions),
            //     'timestamp' => now()->toDateTimeString(),
        // ]);
    }

    private function logPermissionReset(UserRole $role): void
    {
        // Log::info('Permissões resetadas para padrão', [
            //     'role' => $role->value,
            //     'admin_user' => auth()->id(),
            //     'timestamp' => now()->toDateTimeString(),
        // ]);
    }

    private function invalidateRelatedCaches(string $roleName): void
    {
        // Invalidar cache de usuários com este role
        $userIds = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', $roleName)
            ->pluck('model_id');

        foreach ($userIds as $userId) {
            $this->cacheService->clearUserCache($userId);
        }
    }

    private function invalidateAllCaches(): void
    {
        $this->cacheService->clearAllPermissionCaches();
    }
}