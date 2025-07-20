<?php

namespace App\Services;

use App\Models\ScreenPermission;
use App\Services\RouteDiscoveryService;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DynamicPermissionService
{
    private RouteDiscoveryService $routeService;
    
    public function __construct(RouteDiscoveryService $routeService)
    {
        $this->routeService = $routeService;
    }
    
    /**
     * Obter estrutura completa de permissões para interface
     */
    public function getPermissionStructure(): array
    {
        $routes = $this->routeService->getRoutesByModule();
        $roles = $this->getAllRoles();
        $defaultPermissions = $this->routeService->getDefaultPermissionsByRole();
        $currentPermissions = $this->getCurrentPermissions();
        
        return [
            'modules' => $routes,
            'roles' => $roles,
            'defaults' => $defaultPermissions,
            'current' => $currentPermissions,
            'statistics' => $this->getPermissionStatistics()
        ];
    }
    
    /**
     * Salvar configuração de permissões para um role
     */
    public function saveRolePermissions(string $roleName, array $permissions): bool
    {
        try {
            DB::beginTransaction();
            
            // Garantir que o role existe
            $role = $this->ensureRoleExists($roleName);
            
            // Limpar permissões existentes do role na tabela screen_permissions
            ScreenPermission::where('role_name', $roleName)->delete();
            
            // Salvar novas permissões
            foreach ($permissions as $routeName => $hasAccess) {
                if ($hasAccess) {
                    $routeData = $this->findRouteData($routeName);
                    
                    ScreenPermission::create([
                        'role_name' => $roleName,
                        'screen_route' => $routeName,
                        'screen_name' => $routeData['name'] ?? $routeName,
                        'screen_module' => $routeData['module'] ?? 'outros',
                        'can_access' => true,
                        'can_create' => $this->shouldHaveCreatePermission($routeName),
                        'can_edit' => $this->shouldHaveEditPermission($routeName),
                        'can_delete' => $this->shouldHaveDeletePermission($routeName),
                    ]);
                }
            }
            
            // Também salvar no sistema Spatie para compatibilidade
            $this->syncSpatiePermissions($role, $permissions);
            
            // Limpar cache
            $this->clearPermissionCache();
            
            DB::commit();
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao salvar permissões: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Aplicar permissões padrão para um role
     */
    public function applyDefaultPermissions(string $roleName): bool
    {
        $defaults = $this->routeService->getDefaultPermissionsByRole();
        
        if (!isset($defaults[$roleName])) {
            return false;
        }
        
        $roleDefaults = $defaults[$roleName];
        
        // Se é acesso total (ADMIN)
        if ($roleDefaults['default_access'] === 'all') {
            $allRoutes = $this->routeService->discoverWebRoutes();
            $permissions = [];
            
            foreach ($allRoutes as $route) {
                $permissions[$route['route']] = true;
            }
            
            return $this->saveRolePermissions($roleName, $permissions);
        }
        
        // Se é acesso customizado
        if ($roleDefaults['default_access'] === 'custom') {
            return $this->saveRolePermissions($roleName, $roleDefaults['permissions']);
        }
        
        return true;
    }
    
    /**
     * Inicializar sistema com permissões padrão para todos os roles
     */
    public function initializeDefaultPermissions(): array
    {
        $results = [];
        $defaults = $this->routeService->getDefaultPermissionsByRole();
        
        foreach ($defaults as $roleName => $config) {
            $results[$roleName] = $this->applyDefaultPermissions($roleName);
        }
        
        return $results;
    }
    
    /**
     * Obter permissões atuais de um role
     */
    public function getRolePermissions(string $roleName): Collection
    {
        \Log::info('DynamicPermissionService::getRolePermissions called for role: ' . $roleName);
        
        $permissions = ScreenPermission::where('role_name', $roleName)
            ->where('can_access', true)
            ->get();
            
        \Log::info('Found permissions count: ' . $permissions->count());
        \Log::info('Permissions: ' . $permissions->toJson());
        
        return $permissions->keyBy('screen_route');
    }
    
    /**
     * Verificar se usuário tem acesso a uma rota
     */
    public function userCanAccessRoute($user, string $routeName): bool
    {
        // Admin sempre tem acesso
        if ($user->isAdmin()) {
            return true;
        }
        
        // Dashboard sempre liberado
        if ($routeName === 'dashboard') {
            return true;
        }
        
        // Verificar nas permissões
        $roles = $user->getRoleNames();
        
        return ScreenPermission::whereIn('role_name', $roles)
            ->where('screen_route', $routeName)
            ->where('can_access', true)
            ->exists();
    }
    
    /**
     * Obter menu lateral baseado nas permissões do usuário
     */
    public function getUserMenu($user): array
    {
        if ($user->isAdmin()) {
            return $this->getAdminMenu();
        }
        
        $roles = $user->getRoleNames();
        $permissions = ScreenPermission::whereIn('role_name', $roles)
            ->where('can_access', true)
            ->get()
            ->groupBy('screen_module');
        
        return $this->buildMenuFromPermissions($permissions);
    }
    
    /**
     * Obter estatísticas das permissões
     */
    private function getPermissionStatistics(): array
    {
        $totalRoutes = $this->routeService->discoverWebRoutes()->count();
        $totalPermissions = ScreenPermission::count();
        $activePermissions = ScreenPermission::where('can_access', true)->count();
        $roleCount = Role::count();
        
        return [
            'total_routes' => $totalRoutes,
            'total_permissions' => $totalPermissions,
            'active_permissions' => $activePermissions,
            'role_count' => $roleCount,
            'coverage_percentage' => $totalRoutes > 0 ? round(($totalPermissions / ($totalRoutes * $roleCount)) * 100) : 0
        ];
    }
    
    /**
     * Garantir que role existe
     */
    private function ensureRoleExists(string $roleName): Role
    {
        return Role::firstOrCreate(['name' => $roleName]);
    }
    
    /**
     * Encontrar dados de uma rota
     */
    private function findRouteData(string $routeName): ?array
    {
        $routes = $this->routeService->discoverWebRoutes();
        return $routes->firstWhere('route', $routeName);
    }
    
    /**
     * Verificar se rota deve ter permissão de criação
     */
    private function shouldHaveCreatePermission(string $routeName): bool
    {
        return str_contains($routeName, 'create') || str_contains($routeName, 'store');
    }
    
    /**
     * Verificar se rota deve ter permissão de edição
     */
    private function shouldHaveEditPermission(string $routeName): bool
    {
        return str_contains($routeName, 'edit') || str_contains($routeName, 'update');
    }
    
    /**
     * Verificar se rota deve ter permissão de exclusão
     */
    private function shouldHaveDeletePermission(string $routeName): bool
    {
        return str_contains($routeName, 'destroy') || str_contains($routeName, 'delete');
    }
    
    /**
     * Sincronizar com sistema Spatie
     */
    private function syncSpatiePermissions(Role $role, array $permissions): void
    {
        // Criar permissões se não existirem
        foreach ($permissions as $routeName => $hasAccess) {
            if ($hasAccess) {
                Permission::firstOrCreate(['name' => $routeName]);
            }
        }
        
        // Sincronizar permissões do role
        $permissionNames = array_keys(array_filter($permissions));
        $role->syncPermissions($permissionNames);
    }
    
    /**
     * Obter todos os roles
     */
    private function getAllRoles(): Collection
    {
        $defaults = $this->routeService->getDefaultPermissionsByRole();
        
        return collect($defaults)->map(function ($config, $name) {
            return [
                'name' => $name,
                'label' => $this->getRoleLabel($name),
                'description' => $config['description'],
                'default_access' => $config['default_access']
            ];
        });
    }
    
    /**
     * Obter label do role
     */
    private function getRoleLabel(string $roleName): string
    {
        $labels = [
            'ADMIN' => 'Administrador',
            'PARLAMENTAR' => 'Parlamentar',
            'LEGISLATIVO' => 'Legislativo',
            'PROTOCOLO' => 'Protocolo'
        ];
        
        return $labels[$roleName] ?? $roleName;
    }
    
    /**
     * Obter permissões atuais de todos os roles
     */
    private function getCurrentPermissions(): array
    {
        return ScreenPermission::where('can_access', true)
            ->get()
            ->groupBy('role_name')
            ->map(function ($permissions) {
                return $permissions->pluck('screen_route')->toArray();
            })
            ->toArray();
    }
    
    /**
     * Construir menu a partir das permissões
     */
    private function buildMenuFromPermissions($permissions): array
    {
        $menu = [];
        
        foreach ($permissions as $module => $modulePermissions) {
            $menu[$module] = [
                'name' => $this->routeService->getModuleName($module),
                'routes' => $modulePermissions->pluck('screen_name', 'screen_route')->toArray()
            ];
        }
        
        return $menu;
    }
    
    /**
     * Obter menu completo para admin
     */
    private function getAdminMenu(): array
    {
        $routes = $this->routeService->getRoutesByModule();
        $menu = [];
        
        foreach ($routes as $module => $moduleData) {
            $menu[$module] = [
                'name' => $moduleData['name'],
                'routes' => $moduleData['routes']->pluck('name', 'route')->toArray()
            ];
        }
        
        return $menu;
    }
    
    /**
     * Limpar cache de permissões
     */
    private function clearPermissionCache(): void
    {
        // Limpar cache sem usar tags (compatível com file driver)
        Cache::forget('permissions_structure');
        Cache::forget('user_permissions');
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}