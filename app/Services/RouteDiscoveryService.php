<?php

namespace App\Services;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Collection;

class RouteDiscoveryService
{
    /**
     * Detectar todas as rotas web do sistema
     */
    public function discoverWebRoutes(): Collection
    {
        $routes = collect();
        
        // Obter todas as rotas registradas
        $allRoutes = Route::getRoutes();
        
        foreach ($allRoutes as $route) {
            // Filtrar apenas rotas web com nomes
            if ($this->isWebRoute($route) && $route->getName()) {
                $routeData = $this->extractRouteData($route);
                if ($routeData) {
                    $routes->push($routeData);
                }
            }
        }
        
        return $routes->sortBy(['module', 'name']);
    }
    
    /**
     * Agrupar rotas por módulo
     */
    public function getRoutesByModule(): Collection
    {
        return $this->discoverWebRoutes()
            ->groupBy('module')
            ->map(function ($routes, $module) {
                return [
                    'name' => $this->getModuleName($module),
                    'routes' => $routes->keyBy('route')
                ];
            });
    }
    
    /**
     * Obter permissões padrão por perfil
     */
    public function getDefaultPermissionsByRole(): array
    {
        return [
            'ADMIN' => [
                'description' => 'Administrador do sistema - Acesso total',
                'default_access' => 'all', // all, none, custom
                'permissions' => [] // Vazio = todas as permissões
            ],
            'PARLAMENTAR' => [
                'description' => 'Parlamentar - Criação e acompanhamento de proposições',
                'default_access' => 'custom',
                'permissions' => [
                    'dashboard' => true,
                    'proposicoes.create' => true,
                    'proposicoes.index' => true,
                    'proposicoes.show' => true,
                    'proposicoes.edit' => true,
                    'proposicoes.assinatura.index' => true,
                    'proposicoes.assinatura.assinar' => true,
                    'profile.edit' => true,
                ]
            ],
            'LEGISLATIVO' => [
                'description' => 'Setor Legislativo - Revisão e análise de proposições',
                'default_access' => 'custom',
                'permissions' => [
                    'dashboard' => true,
                    'proposicoes.legislativo.index' => true,
                    'proposicoes.legislativo.revisar' => true,
                    'proposicoes.legislativo.aprovar' => true,
                    'proposicoes.legislativo.devolver' => true,
                    'proposicoes.show' => true,
                    'profile.edit' => true,
                ]
            ],
            'PROTOCOLO' => [
                'description' => 'Setor de Protocolo - Protocolo e distribuição',
                'default_access' => 'custom',
                'permissions' => [
                    'dashboard' => true,
                    'proposicoes.protocolo.index' => true,
                    'proposicoes.protocolo.protocolar' => true,
                    'proposicoes.protocolo.distribuir' => true,
                    'proposicoes.show' => true,
                    'profile.edit' => true,
                ]
            ]
        ];
    }
    
    /**
     * Verificar se é uma rota web válida
     */
    private function isWebRoute($route): bool
    {
        $middleware = $route->middleware();
        
        // Deve ter middleware web e não ser API
        return in_array('web', $middleware) && 
               !in_array('api', $middleware) &&
               !str_starts_with($route->uri(), 'api/');
    }
    
    /**
     * Extrair dados da rota
     */
    private function extractRouteData($route): ?array
    {
        $name = $route->getName();
        $uri = $route->uri();
        
        // Ignorar rotas do sistema, debug, etc
        if ($this->shouldIgnoreRoute($name, $uri)) {
            return null;
        }
        
        return [
            'route' => $name,
            'uri' => $uri,
            'name' => $this->getRouteFriendlyName($name),
            'module' => $this->extractModule($name),
            'methods' => $route->methods(),
            'action' => $this->extractAction($name),
        ];
    }
    
    /**
     * Verificar se deve ignorar a rota
     */
    private function shouldIgnoreRoute(string $name, string $uri): bool
    {
        $ignoredPrefixes = [
            'debugbar.',
            'telescope.',
            '_debugbar',
            'ignition.',
            'livewire.',
            'filament.',
            'sanctum.',
            'password.',
            'verification.',
        ];
        
        $ignoredRoutes = [
            'login',
            'logout',
            'register',
            'password.request',
            'password.email',
            'password.reset',
            'password.confirm',
        ];
        
        // Verificar prefixos ignorados
        foreach ($ignoredPrefixes as $prefix) {
            if (str_starts_with($name, $prefix)) {
                return true;
            }
        }
        
        // Verificar rotas específicas ignoradas
        if (in_array($name, $ignoredRoutes)) {
            return true;
        }
        
        // Ignorar rotas com parâmetros dinâmicos demais
        if (substr_count($uri, '{') > 2) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Extrair módulo da rota
     */
    private function extractModule(string $routeName): string
    {
        $parts = explode('.', $routeName);
        
        // Mapear módulos conhecidos
        $moduleMap = [
            'dashboard' => 'dashboard',
            'proposicoes' => 'proposicoes',
            'parlamentares' => 'parlamentares',
            'usuarios' => 'usuarios',
            'admin' => 'admin',
            'profile' => 'profile',
            'relatorios' => 'relatorios',
        ];
        
        $firstPart = $parts[0] ?? 'outros';
        
        return $moduleMap[$firstPart] ?? $firstPart;
    }
    
    /**
     * Extrair ação da rota
     */
    private function extractAction(string $routeName): string
    {
        $parts = explode('.', $routeName);
        $lastPart = end($parts);
        
        $actionMap = [
            'index' => 'listar',
            'create' => 'criar',
            'store' => 'salvar',
            'show' => 'visualizar',
            'edit' => 'editar',
            'update' => 'atualizar',
            'destroy' => 'excluir',
            'delete' => 'excluir',
        ];
        
        return $actionMap[$lastPart] ?? $lastPart;
    }
    
    /**
     * Obter nome amigável da rota
     */
    private function getRouteFriendlyName(string $routeName): string
    {
        $names = [
            'dashboard' => 'Dashboard',
            'proposicoes.index' => 'Listar Proposições',
            'proposicoes.create' => 'Criar Proposição',
            'proposicoes.show' => 'Ver Proposição',
            'proposicoes.edit' => 'Editar Proposição',
            'proposicoes.legislativo.index' => 'Revisão Legislativa',
            'proposicoes.legislativo.revisar' => 'Revisar Proposição',
            'proposicoes.legislativo.aprovar' => 'Aprovar Proposição',
            'proposicoes.legislativo.devolver' => 'Devolver Proposição',
            'proposicoes.assinatura.index' => 'Assinatura Digital',
            'proposicoes.assinatura.assinar' => 'Assinar Proposição',
            'proposicoes.protocolo.index' => 'Protocolo',
            'proposicoes.protocolo.protocolar' => 'Protocolar Proposição',
            'proposicoes.protocolo.distribuir' => 'Distribuir Proposição',
            'parlamentares.index' => 'Listar Parlamentares',
            'parlamentares.create' => 'Criar Parlamentar',
            'parlamentares.show' => 'Ver Parlamentar',
            'parlamentares.edit' => 'Editar Parlamentar',
            'usuarios.index' => 'Listar Usuários',
            'usuarios.create' => 'Criar Usuário',
            'usuarios.show' => 'Ver Usuário',
            'usuarios.edit' => 'Editar Usuário',
            'profile.edit' => 'Editar Perfil',
            'admin.dashboard' => 'Admin Dashboard',
            'admin.usuarios.index' => 'Admin Usuários',
            'admin.screen-permissions.index' => 'Gerenciar Permissões',
        ];
        
        return $names[$routeName] ?? $this->generateFriendlyName($routeName);
    }
    
    /**
     * Gerar nome amigável automaticamente
     */
    private function generateFriendlyName(string $routeName): string
    {
        $parts = explode('.', $routeName);
        $readable = [];
        
        foreach ($parts as $part) {
            $readable[] = ucfirst(str_replace(['-', '_'], ' ', $part));
        }
        
        return implode(' - ', $readable);
    }
    
    /**
     * Obter nome do módulo
     */
    public function getModuleName(string $module): string
    {
        $moduleNames = [
            'dashboard' => 'Dashboard',
            'proposicoes' => 'Proposições',
            'parlamentares' => 'Parlamentares',
            'usuarios' => 'Usuários',
            'admin' => 'Administração',
            'profile' => 'Perfil do Usuário',
            'relatorios' => 'Relatórios',
            'outros' => 'Outros',
        ];
        
        return $moduleNames[$module] ?? ucfirst($module);
    }
}