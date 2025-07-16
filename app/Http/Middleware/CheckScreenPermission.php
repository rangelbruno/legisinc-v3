<?php

namespace App\Http\Middleware;

use App\Services\PermissionCacheService;
use App\Models\ScreenPermission;
use App\Models\User;
use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckScreenPermission
{
    public function __construct(
        private PermissionCacheService $permissionCache
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $screen = null, string $action = 'view'): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Admin sempre tem acesso total
        // Verificar se é admin via email ou configuração
        if ($user->email === 'admin@sistema.gov.br' || str_contains($user->email, 'admin')) {
            return $next($request);
        }

        // Determinar tela e ação a verificar
        $screenToCheck = $screen ?? $this->getScreenFromRoute($request);
        $actionToCheck = $this->getActionFromRequest($request, $action);

        // Verificar permissão usando o sistema de telas
        $hasPermission = $this->checkScreenPermission($user, $screenToCheck, $actionToCheck);

        if (!$hasPermission) {
            $this->logAccessDenied($user, $screenToCheck, $actionToCheck, $request);
            
            // Se for uma requisição AJAX, retornar erro JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => 'Acesso negado',
                    'message' => "Você não tem permissão para {$actionToCheck} na tela: {$screenToCheck}",
                    'required_permission' => "{$screenToCheck}.{$actionToCheck}"
                ], 403);
            }
            
            return redirect()->route('dashboard')->with('error', 
                "Você não tem permissão para acessar esta funcionalidade ({$screenToCheck}.{$actionToCheck})."
            );
        }

        return $next($request);
    }

    /**
     * Verificar permissão usando sistema de telas
     */
    private function checkScreenPermission($user, string $screen, string $action): bool
    {
        $roleName = $user->getRoleNames()->first() ?? 'PUBLICO';
        
        // Verificar se há permissões configuradas para este perfil
        if (ScreenPermission::hasConfiguredPermissions($roleName)) {
            // Sistema de permissões por tela - verificar se a tela está liberada
            $routeToCheck = $this->getRouteFromScreenAction($screen, $action);
            
            // Verificar se pode acessar a rota específica
            if (ScreenPermission::userCanAccessRoute($routeToCheck)) {
                return true;
            }
            
            // Se não encontrou a rota específica, verificar se tem acesso ao módulo
            $moduleAccess = ScreenPermission::userCanAccessModule($this->getModuleFromScreen($screen));
            
            // Para parlamentares, permitir criar projetos se tem acesso ao módulo projetos
            if ($roleName === 'PARLAMENTAR' && $screen === 'projetos' && $action === 'create' && $moduleAccess) {
                return true;
            }
            
            return $moduleAccess;
        }

        // Se não há permissões configuradas, usar regras padrão
        return $this->checkDefaultPermissions($roleName, $screen, $action);
    }

    /**
     * Verificar permissões padrão quando não há configuração específica
     */
    private function checkDefaultPermissions(string $roleName, string $screen, string $action): bool
    {
        // Dashboard sempre acessível
        if ($screen === 'dashboard') {
            return true;
        }

        // Regras padrão por perfil
        switch ($roleName) {
            case 'ADMIN':
                return true;
            
            case 'LEGISLATIVO':
                return true; // Acesso total
            
            case 'PARLAMENTAR':
                $allowedScreens = [
                    'parlamentares' => ['view'],
                    'projetos' => ['view', 'create', 'edit'], // Parlamentar pode criar e editar projetos
                    'comissoes' => ['view'],
                    'sessoes' => ['view', 'create'],
                ];
                
                return isset($allowedScreens[$screen]) && in_array($action, $allowedScreens[$screen]);
            
            case 'RELATOR':
                $allowedScreens = [
                    'parlamentares' => ['view'],
                    'projetos' => ['view', 'create', 'edit'], // Relator pode criar e editar projetos
                    'comissoes' => ['view'],
                    'sessoes' => ['view'],
                ];
                
                return isset($allowedScreens[$screen]) && in_array($action, $allowedScreens[$screen]);
            
            case 'PROTOCOLO':
                $allowedScreens = [
                    'projetos' => ['view', 'create'],
                    'sessoes' => ['view', 'create'],
                ];
                
                return isset($allowedScreens[$screen]) && in_array($action, $allowedScreens[$screen]);
            
            case 'ASSESSOR':
                $allowedScreens = [
                    'parlamentares' => ['view'],
                    'projetos' => ['view'],
                    'comissoes' => ['view'],
                    'sessoes' => ['view'],
                ];
                
                return isset($allowedScreens[$screen]) && in_array($action, $allowedScreens[$screen]);
                
            default:
                return false;
        }
    }

    /**
     * Converter tela + ação em rota
     */
    private function getRouteFromScreenAction(string $screen, string $action): string
    {
        if ($action === 'view') {
            return "{$screen}.index";
        }
        
        return "{$screen}.{$action}";
    }

    /**
     * Extrair módulo da tela
     */
    private function getModuleFromScreen(string $screen): string
    {
        return explode('.', $screen)[0];
    }

    /**
     * Extrair nome da tela da rota atual
     */
    private function getScreenFromRoute(Request $request): string
    {
        $routeName = $request->route()->getName();
        
        if (!$routeName) {
            return 'unknown';
        }

        // Remover sufixos comuns (.store, .update, .destroy, etc.)
        $screen = preg_replace('/\.(store|update|destroy|show)$/', '', $routeName);
        
        return $screen;
    }

    /**
     * Determinar ação baseada no método HTTP e rota
     */
    private function getActionFromRequest(Request $request, string $defaultAction): string
    {
        $method = $request->method();
        $routeName = $request->route()->getName();
        
        // Mapear ações baseado na rota e método HTTP
        if (str_ends_with($routeName, '.create') || str_ends_with($routeName, '.store')) {
            return 'create';
        }
        
        if (str_ends_with($routeName, '.edit') || str_ends_with($routeName, '.update')) {
            return 'edit';
        }
        
        if (str_ends_with($routeName, '.destroy') || $method === 'DELETE') {
            return 'delete';
        }
        
        // Mapear por método HTTP
        return match($method) {
            'POST' => 'create',
            'PUT', 'PATCH' => 'edit',
            'DELETE' => 'delete',
            default => $defaultAction
        };
    }

    /**
     * Registrar tentativa de acesso negado para auditoria
     */
    private function logAccessDenied($user, string $screen, string $action, Request $request): void
    {
        Log::warning('Acesso negado', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_roles' => $user->getRoleNames()->toArray(),
            'screen' => $screen,
            'action' => $action,
            'route' => $request->route()->getName(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
}
