<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

/**
 * Middleware de autenticação híbrida que suporta tanto sessão quanto token personalizado
 */
class AuthenticateToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = null;
        $authMethod = 'none';
        
        // Tentar autenticação por token primeiro (para AJAX)
        $token = $request->bearerToken() ?: $request->header('X-Auth-Token');
        
        if ($token && str_starts_with($token, 'ajx_')) {
            $tokenData = Cache::get('auth_token:' . $token);
            
            if ($tokenData) {
                $user = User::find($tokenData['user_id']);
                $authMethod = 'token';
            }
        }
        
        // Se não autenticou por token, tentar por sessão
        if (!$user && Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            $authMethod = 'session';
        }
        
        if (!$user) {
            return $this->handleUnauthenticated($request);
        }
        
        // Adicionar informações de autenticação no request
        $request->merge([
            '_auth_method' => $authMethod,
            '_auth_user' => $user
        ]);
        
        // Definir usuário autenticado
        Auth::setUser($user);
        
        return $next($request);
    }
    
    /**
     * Handle unauthenticated request
     */
    private function handleUnauthenticated(Request $request): Response
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'error' => 'Unauthenticated',
                'message' => 'Token de autenticação inválido ou expirado',
                'code' => 'TOKEN_EXPIRED'
            ], 401);
        }
        
        return redirect()->route('login');
    }
} 