<?php

namespace App\Http\Middleware;

use App\Services\PermissionCacheService;
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
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $screen = null, string $action = 'view'): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Admin sempre tem acesso total
        if ($user->hasRole(UserRole::ADMIN->value)) {
            return $next($request);
        }

        // Determinar tela e ação a verificar
        $screenToCheck = $screen ?? $this->getScreenFromRoute($request);
        $actionToCheck = $this->getActionFromRequest($request, $action);

        // Verificação em cache com fallback
        $hasPermission = $this->permissionCache->userHasScreenPermission(
            $user->id,
            $screenToCheck,
            $actionToCheck
        );

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
