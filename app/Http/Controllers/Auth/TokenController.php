<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Controller para gerenciamento de tokens de autenticação personalizados
 */
class TokenController extends Controller
{
    /**
     * Gerar novo token de autenticação para o usuário atual
     */
    public function generateToken(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'error' => 'Usuário não autenticado',
                'code' => 'USER_NOT_AUTHENTICATED'
            ], 401);
        }
        
        // Gerar token único
        $token = 'ajx_' . Str::random(60);
        
        // Armazenar token no cache por 1 hora (ajustável)
        $tokenData = [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'created_at' => now()->toDateTimeString(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ];
        
        Cache::put('auth_token:' . $token, $tokenData, now()->addHour());
        
        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ],
            'expires_in' => 3600 // 1 hora em segundos
        ]);
    }
    
    /**
     * Verificar se token atual é válido
     */
    public function verifyToken(Request $request): JsonResponse
    {
        $token = $request->bearerToken() ?: $request->header('X-Auth-Token');
        
        if (!$token || !str_starts_with($token, 'ajx_')) {
            return response()->json([
                'valid' => false,
                'error' => 'Token inválido',
                'code' => 'INVALID_TOKEN_FORMAT'
            ], 401);
        }
        
        $tokenData = Cache::get('auth_token:' . $token);
        
        if (!$tokenData) {
            return response()->json([
                'valid' => false,
                'error' => 'Token expirado ou inválido',
                'code' => 'TOKEN_EXPIRED'
            ], 401);
        }
        
        return response()->json([
            'valid' => true,
            'user' => [
                'id' => $tokenData['user_id'],
                'name' => $tokenData['user_name'],
                'email' => $tokenData['user_email']
            ],
            'created_at' => $tokenData['created_at']
        ]);
    }
    
    /**
     * Revogar token atual
     */
    public function revokeToken(Request $request): JsonResponse
    {
        $token = $request->bearerToken() ?: $request->header('X-Auth-Token');
        
        if (!$token) {
            return response()->json([
                'error' => 'Token não fornecido',
                'code' => 'NO_TOKEN'
            ], 400);
        }
        
        Cache::forget('auth_token:' . $token);
        
        return response()->json([
            'success' => true,
            'message' => 'Token revogado com sucesso'
        ]);
    }
    
    /**
     * Obter novo token via AJAX (para páginas já carregadas)
     */
    public function getAjaxToken(Request $request): JsonResponse
    {
        // Verificar se usuário está autenticado via sessão
        if (!Auth::check()) {
            return response()->json([
                'error' => 'Usuário não autenticado via sessão',
                'code' => 'SESSION_EXPIRED',
                'redirect_to' => route('login')
            ], 401);
        }
        
        return $this->generateToken($request);
    }
} 