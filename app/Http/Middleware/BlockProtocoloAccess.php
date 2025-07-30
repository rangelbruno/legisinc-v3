<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BlockProtocoloAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar se o usuário está autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Você precisa estar logado para acessar esta página.');
        }

        $user = Auth::user();

        // Bloquear acesso de usuários com perfil PROTOCOLO
        if ($user->perfil === 'PROTOCOLO') {
            // Se for requisição AJAX, retornar JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado. Usuários do protocolo não têm acesso a esta funcionalidade.'
                ], 403);
            }

            // Para requisições normais, redirecionar com mensagem de erro
            return redirect()->route('dashboard')
                ->with('error', 'Acesso negado. Usuários do protocolo não têm acesso a esta funcionalidade.');
        }

        return $next($request);
    }
}
