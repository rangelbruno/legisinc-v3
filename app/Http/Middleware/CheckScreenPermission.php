<?php

namespace App\Http\Middleware;

use App\Services\PermissionCacheService;
use App\Models\ScreenPermission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckScreenPermission
{
    public function __construct(
        private PermissionCacheService $permissionCache
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ?string $screen = null, string $action = 'view'): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Admin sempre tem acesso total
        // Verificar se tem role de administrador usando método seguro
        if ($user->hasRole(['ADMIN', 'Administrador'])) {
            return $next($request);
        }

        // Determinar tela e ação a verificar
        $screenToCheck = $screen ?? $this->getScreenFromRoute($request);
        $actionToCheck = $this->getActionFromRequest($request, $action);

        // Log para debug
        \Illuminate\Support\Facades\Log::info('CheckScreenPermission - Debug', [
            'user_id' => $user->id,
            'user_roles' => $user->getRoleNames()->toArray(),
            'screen_to_check' => $screenToCheck,
            'action_to_check' => $actionToCheck,
            'route_name' => $request->route()->getName(),
            'method' => $request->method()
        ]);

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
        $roleName = $user->roles->first()->name ?? 'PUBLICO';
        
        \Illuminate\Support\Facades\Log::info('CheckScreenPermission - checkScreenPermission', [
            'role_name' => $roleName,
            'screen' => $screen,
            'action' => $action
        ]);
        
        // Verificar se há permissões configuradas para este perfil
        if (ScreenPermission::hasConfiguredPermissions($roleName)) {
            // Sistema de permissões por tela - verificar se a tela está liberada
            $routeToCheck = $this->getRouteFromScreenAction($screen, $action);
            
            \Illuminate\Support\Facades\Log::info('CheckScreenPermission - ScreenPermission configurado', [
                'route_to_check' => $routeToCheck,
                'module' => $this->getModuleFromScreen($screen)
            ]);
            
            // Verificar se pode acessar a rota específica
            if (ScreenPermission::userCanAccessRoute($routeToCheck)) {
                \Illuminate\Support\Facades\Log::info('CheckScreenPermission - Acesso permitido por rota específica');
                return true;
            }
            
            // Se não encontrou a rota específica, verificar se tem acesso ao módulo
            $moduleAccess = ScreenPermission::userCanAccessModule($this->getModuleFromScreen($screen));
            
            \Illuminate\Support\Facades\Log::info('CheckScreenPermission - Verificação de módulo', [
                'module_access' => $moduleAccess
            ]);
            
            // Para parlamentares, permitir criar projetos/proposições se tem acesso ao módulo
            if ($roleName === 'PARLAMENTAR' && ($screen === 'projetos' || $screen === 'proposicoes') && $action === 'create' && $moduleAccess) {
                \Illuminate\Support\Facades\Log::info('CheckScreenPermission - Acesso permitido para parlamentar criar');
                return true;
            }
            
            return $moduleAccess;
        }

        \Illuminate\Support\Facades\Log::info('CheckScreenPermission - Usando permissões padrão');
        // Se não há permissões configuradas, usar regras padrão
        return $this->checkDefaultPermissions($roleName, $screen, $action);
    }

    /**
     * Verificar permissões padrão quando não há configuração específica
     */
    private function checkDefaultPermissions(string $roleName, string $screen, string $action): bool
    {
        \Illuminate\Support\Facades\Log::info('CheckScreenPermission - checkDefaultPermissions', [
            'role_name' => $roleName,
            'screen' => $screen,
            'action' => $action
        ]);
        
        // Dashboard sempre acessível
        if ($screen === 'dashboard') {
            \Illuminate\Support\Facades\Log::info('CheckScreenPermission - Dashboard sempre acessível');
            return true;
        }

        // Regras padrão por perfil
        switch ($roleName) {
            case 'ADMIN':
                \Illuminate\Support\Facades\Log::info('CheckScreenPermission - ADMIN tem acesso total');
                return true;
            
            case 'LEGISLATIVO':
                \Illuminate\Support\Facades\Log::info('CheckScreenPermission - LEGISLATIVO tem acesso total');
                return true; // Acesso total
            
            case 'PARLAMENTAR':
                $allowedScreens = [
                    'parlamentares' => ['view'],
                    'projetos' => ['view', 'create', 'edit'], // Parlamentar pode criar e editar projetos
                    'proposicoes' => ['view', 'create', 'edit'], // Parlamentar pode criar e editar proposições
                    'proposicoes.assinatura-digital' => ['view', 'create'], // Parlamentar pode acessar assinatura digital
                    'comissoes' => ['view'],
                    'sessoes' => ['view', 'create'],
                ];
                
                \Illuminate\Support\Facades\Log::info('CheckScreenPermission - PARLAMENTAR verificando', [
                    'allowed_screens' => $allowedScreens,
                    'screen' => $screen,
                    'action' => $action
                ]);
                
                // Verificar se é uma tela específica de assinatura digital
                if (str_starts_with($screen, 'proposicoes.assinatura-digital')) {
                    $result = in_array($action, ['view', 'create']);
                    \Illuminate\Support\Facades\Log::info('CheckScreenPermission - Assinatura digital', [
                        'result' => $result,
                        'action_allowed' => in_array($action, ['view', 'create'])
                    ]);
                    return $result;
                }
                
                $result = isset($allowedScreens[$screen]) && in_array($action, $allowedScreens[$screen]);
                \Illuminate\Support\Facades\Log::info('CheckScreenPermission - Resultado final', [
                    'result' => $result,
                    'screen_exists' => isset($allowedScreens[$screen]),
                    'action_allowed' => isset($allowedScreens[$screen]) ? in_array($action, $allowedScreens[$screen]) : false
                ]);
                return $result;
            
            case 'RELATOR':
                $allowedScreens = [
                    'parlamentares' => ['view'],
                    'projetos' => ['view', 'create', 'edit'], // Relator pode criar e editar projetos
                    'proposicoes' => ['view', 'create', 'edit'], // Relator pode criar e editar proposições
                    'comissoes' => ['view'],
                    'sessoes' => ['view'],
                ];
                
                return isset($allowedScreens[$screen]) && in_array($action, $allowedScreens[$screen]);
            
            case 'PROTOCOLO':
                $allowedScreens = [
                    'projetos' => ['view', 'create'],
                    'proposicoes' => ['view', 'create'],
                    'sessoes' => ['view', 'create'],
                ];
                
                return isset($allowedScreens[$screen]) && in_array($action, $allowedScreens[$screen]);
            
            case 'ASSESSOR':
                $allowedScreens = [
                    'parlamentares' => ['view'],
                    'projetos' => ['view'],
                    'proposicoes' => ['view'],
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

        // Remover sufixos comuns (.store, .update, .destroy, .create, .criar, etc.)
        $screen = preg_replace('/\.(store|update|destroy|show|create|criar|edit|index)$/', '', $routeName);
        
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
        // Log::warning('Acesso negado', [
        //     'user_id' => $user->id,
        //     'user_email' => $user->email,
        //     'user_roles' => $user->getRoleNames()->toArray(),
        //     'screen' => $screen,
        //     'action' => $action,
        //     'route' => $request->route()->getName(),
        //     'method' => $request->method(),
        //     'ip' => $request->ip(),
        //     'user_agent' => $request->userAgent(),
        //     'url' => $request->fullUrl(),
        //     'timestamp' => now()->toDateTimeString(),
        // ]);
    }
}
