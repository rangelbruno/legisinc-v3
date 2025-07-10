<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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

        // Verificar se o usuário tem permissão
        if (!$this->userHasPermission($user, $permission)) {
            return redirect()->route('dashboard')->with('error', 'Você não tem permissão para acessar esta página.');
        }

        return $next($request);
    }

    /**
     * Check if user has a specific permission
     */
    private function userHasPermission($user, string $permission): bool
    {
        // Get user's roles
        $userRoles = DB::table('model_has_roles')
            ->where('model_type', 'App\\Models\\User')
            ->where('model_id', $user->id)
            ->pluck('role_id');

        if ($userRoles->isEmpty()) {
            return false;
        }

        // Check if any of the user's roles has the permission
        $hasPermission = DB::table('role_has_permissions')
            ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->whereIn('role_has_permissions.role_id', $userRoles)
            ->where('permissions.name', $permission)
            ->exists();

        return $hasPermission;
    }
}