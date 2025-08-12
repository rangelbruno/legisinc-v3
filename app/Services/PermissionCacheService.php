<?php

namespace App\Services;

use App\Models\User;
use App\Models\ScreenPermission;
use App\Enums\UserRole;
use App\Enums\SystemModule;
use App\Enums\PermissionAction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PermissionCacheService
{
    private const CACHE_TTL = 3600; // 1 hora
    private const CACHE_PREFIX = 'user_permissions_';
    private const CACHE_HIT_KEY = 'permission_cache_hits';
    private const CACHE_MISS_KEY = 'permission_cache_misses';

    public function userHasScreenPermission(int $userId, string $screen, string $action = 'view'): bool
    {
        $cacheKey = self::CACHE_PREFIX . $userId;
        
        $permissions = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId) {
            $this->incrementCacheMisses();
            return $this->loadUserPermissions($userId);
        });

        if (Cache::has($cacheKey)) {
            $this->incrementCacheHits();
        }

        $permissionKey = "{$screen}.{$action}";
        $hasPermission = $permissions[$permissionKey] ?? false;

        // Log para auditoria se acesso negado
        if (!$hasPermission) {
            $this->logAccessAttempt($userId, $screen, $action, false);
        }

        return $hasPermission;
    }

    public function userHasModuleAccess(int $userId, string $module): bool
    {
        $cacheKey = self::CACHE_PREFIX . $userId;
        
        $permissions = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId) {
            return $this->loadUserPermissions($userId);
        });

        // Verificar se tem acesso a qualquer rota do módulo
        foreach ($permissions as $permissionKey => $hasAccess) {
            if ($hasAccess && str_starts_with($permissionKey, $module . '.')) {
                return true;
            }
        }

        return false;
    }

    public function getUserPermissionMatrix(int $userId): array
    {
        $cacheKey = self::CACHE_PREFIX . $userId . '_matrix';
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId) {
            $permissions = $this->loadUserPermissions($userId);
            $matrix = [];

            foreach (SystemModule::cases() as $module) {
                $moduleData = [
                    'module' => $module->value,
                    'label' => $module->getLabel(),
                    'icon' => $module->getIconClass(),
                    'color' => $module->getColor(),
                    'screens' => [],
                    'totalScreens' => 0,
                    'activeScreens' => 0,
                ];

                foreach ($module->getRoutes() as $route => $name) {
                    $screenPermissions = [];
                    $hasAnyAccess = false;

                    foreach (PermissionAction::cases() as $action) {
                        $permissionKey = "{$route}.{$action->value}";
                        $hasPermission = $permissions[$permissionKey] ?? false;
                        $screenPermissions[$action->value] = $hasPermission;
                        
                        if ($hasPermission) {
                            $hasAnyAccess = true;
                        }
                    }

                    $moduleData['screens'][] = [
                        'route' => $route,
                        'name' => $name,
                        'permissions' => $screenPermissions,
                        'hasAccess' => $hasAnyAccess,
                    ];

                    $moduleData['totalScreens']++;
                    if ($hasAnyAccess) {
                        $moduleData['activeScreens']++;
                    }
                }

                $moduleData['percentage'] = $moduleData['totalScreens'] > 0 
                    ? round(($moduleData['activeScreens'] / $moduleData['totalScreens']) * 100) 
                    : 0;

                $matrix[$module->value] = $moduleData;
            }

            return $matrix;
        });
    }

    private function loadUserPermissions(int $userId): array
    {
        $user = User::with('roles')->find($userId);
        
        if (!$user) {
            return [];
        }

        // Admin tem acesso total
        if ($user->hasRole(UserRole::ADMIN->value)) {
            return $this->getAllPermissions();
        }

        $permissions = [];

        foreach ($user->roles as $role) {
            $rolePermissions = $this->loadRolePermissions($role->name);
            $permissions = array_merge($permissions, $rolePermissions);
        }

        return $permissions;
    }

    private function loadRolePermissions(string $roleName): array
    {
        $permissions = [];
        
        $screenPermissions = ScreenPermission::where('role_name', $roleName)
            ->get()
            ->keyBy('screen_route');

        foreach ($screenPermissions as $route => $permission) {
            if ($permission->can_access) {
                $permissions["{$route}.view"] = true;
            }
            if ($permission->can_create ?? false) {
                $permissions["{$route}.create"] = true;
            }
            if ($permission->can_edit ?? false) {
                $permissions["{$route}.edit"] = true;
            }
            if ($permission->can_delete ?? false) {
                $permissions["{$route}.delete"] = true;
            }
        }

        // Aplicar permissões padrão se não existirem específicas
        if (empty($permissions)) {
            $permissions = $this->getDefaultRolePermissions($roleName);
        }

        return $permissions;
    }

    private function getDefaultRolePermissions(string $roleName): array
    {
        try {
            $role = UserRole::from($roleName);
            $defaultPermissions = $role->getDefaultPermissions();
            $permissions = [];

            foreach ($defaultPermissions as $permission) {
                if ($permission === '*') {
                    return $this->getAllPermissions();
                }

                // Converter permissão padrão para formato específico
                if (str_contains($permission, '*')) {
                    $module = str_replace('.*', '', $permission);
                    $moduleEnum = SystemModule::tryFrom($module);
                    
                    if ($moduleEnum) {
                        foreach ($moduleEnum->getRoutes() as $route => $name) {
                            foreach (PermissionAction::cases() as $action) {
                                $permissions["{$route}.{$action->value}"] = true;
                            }
                        }
                    }
                } else {
                    $permissions[$permission] = true;
                }
            }

            return $permissions;
        } catch (\ValueError $e) {
            // Log::warning("Role inválido: {$roleName}");
            return [];
        }
    }

    private function getAllPermissions(): array
    {
        $permissions = [];
        
        foreach (SystemModule::cases() as $module) {
            foreach ($module->getRoutes() as $route => $name) {
                foreach (PermissionAction::cases() as $action) {
                    $permissions["{$route}.{$action->value}"] = true;
                }
            }
        }

        return $permissions;
    }

    public function clearUserCache(int $userId): void
    {
        $cacheKey = self::CACHE_PREFIX . $userId;
        $matrixKey = self::CACHE_PREFIX . $userId . '_matrix';
        
        Cache::forget($cacheKey);
        Cache::forget($matrixKey);
        
        // Log::info("Cache de permissões limpo para usuário {$userId}");
    }

    public function clearAllPermissionCaches(): void
    {
        try {
            // Limpar apenas cache de estatísticas e matriz de permissões
            Cache::forget('permissions_statistics');
            Cache::forget('permissions_matrix');
            
            // Limpar caches de usuários ativos recentemente para otimizar performance
            $userIds = User::where('updated_at', '>', now()->subWeek())->pluck('id');
            foreach ($userIds as $userId) {
                $this->clearUserCache($userId);
            }
            
            // Log::info("Caches de permissões limpos para " . count($userIds) . " usuários");
        } catch (\Exception $e) {
            // Log::error("Erro ao limpar caches de permissões: " . $e->getMessage());
        }
    }

    public function warmUserCache(int $userId): void
    {
        $cacheKey = self::CACHE_PREFIX . $userId;
        
        // Força o carregamento do cache
        Cache::forget($cacheKey);
        $this->loadUserPermissions($userId);
        
        // Log::info("Cache aquecido para usuário {$userId}");
    }

    public function getCacheStatistics(): array
    {
        $hits = Cache::get(self::CACHE_HIT_KEY, 0);
        $misses = Cache::get(self::CACHE_MISS_KEY, 0);
        $total = $hits + $misses;
        
        return [
            'hits' => $hits,
            'misses' => $misses,
            'total' => $total,
            'hit_ratio' => $total > 0 ? round(($hits / $total) * 100, 2) : 0,
        ];
    }

    public function resetCacheStatistics(): void
    {
        Cache::forget(self::CACHE_HIT_KEY);
        Cache::forget(self::CACHE_MISS_KEY);
    }

    private function incrementCacheHits(): void
    {
        Cache::increment(self::CACHE_HIT_KEY);
    }

    private function incrementCacheMisses(): void
    {
        Cache::increment(self::CACHE_MISS_KEY);
    }

    private function logAccessAttempt(int $userId, string $screen, string $action, bool $granted): void
    {
        try {
            // Verificar se a tabela existe antes de tentar inserir
            if (Schema::hasTable('permission_access_log')) {
                DB::table('permission_access_log')->insert([
                    'user_id' => $userId,
                    'screen_route' => $screen,
                    'action' => $action,
                    'status' => $granted ? 'granted' : 'denied',
                    'ip_address' => request()?->ip(),
                    'user_agent' => request()?->userAgent(),
                    'created_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            // Log::error("Erro ao registrar tentativa de acesso: " . $e->getMessage());
        }
    }

    public function preloadActiveUserPermissions(): int
    {
        $count = 0;
        
        $activeUsers = User::where('last_activity', '>', now()->subHours(24))
            ->orWhere('updated_at', '>', now()->subHours(24))
            ->pluck('id');

        foreach ($activeUsers as $userId) {
            $this->warmUserCache($userId);
            $count++;
        }

        // Log::info("Cache pré-carregado para {$count} usuários ativos");
        return $count;
    }
}