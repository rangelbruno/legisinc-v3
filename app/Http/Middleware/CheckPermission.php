<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Admin sempre tem acesso total
        $userRoles = $user->getRoleNames();
        
        // Log temporário para debug
        Log::info('CheckPermission Debug', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'roles' => $userRoles->toArray(),
            'permission_requested' => $permission,
            'route' => $request->route()->getName()
        ]);
        
        if ($userRoles->contains('ADMIN') || $userRoles->contains('Administrador')) {
            Log::info('Admin access granted', ['user' => $user->email]);
            return $next($request);
        }

        // Verificar se o usuário tem permissão
        if (!$user->hasPermissionTo($permission)) {
            return redirect()->route('dashboard')->with('error', 'Você não tem permissão para acessar esta página.');
        }

        return $next($request);
    }

    // Removed userHasPermission method - now using User model's hasPermissionTo method directly
}