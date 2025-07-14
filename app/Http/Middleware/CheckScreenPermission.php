<?php

namespace App\Http\Middleware;

use App\Models\ScreenPermission;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckScreenPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Admin sempre tem acesso a tudo
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Obter a rota atual
        $currentRoute = $request->route()->getName();
        
        // Obter o perfil do usuário
        $userRole = $user->getRoleNames()->first();
        
        if (!$userRole) {
            return redirect()->route('dashboard')->with('error', 'Usuário sem perfil definido.');
        }

        // Verificar se o usuário tem permissão para acessar esta tela
        $hasAccess = $this->checkAccess($userRole, $currentRoute);
        
        if (!$hasAccess) {
            // Se for uma requisição AJAX, retornar erro JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => 'Acesso negado',
                    'message' => 'Você não tem permissão para acessar esta funcionalidade.'
                ], 403);
            }
            
            return redirect()->route('dashboard')->with('error', 'Você não tem permissão para acessar esta página.');
        }

        return $next($request);
    }

    /**
     * Verificar se o usuário tem acesso à rota
     */
    private function checkAccess(string $userRole, string $route): bool
    {
        // Usar o método centralizado do ScreenPermission
        return ScreenPermission::userCanAccessRoute($route);
    }

}
