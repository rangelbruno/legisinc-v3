<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckParlamentarAccess
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

        // Verificar se o usuário tem perfil de parlamentar
        if ($user->perfil !== 'PARLAMENTAR') {
            // Se for requisição AJAX, retornar JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado. Apenas parlamentares podem criar proposições.'
                ], 403);
            }

            // Para requisições normais, redirecionar com mensagem de erro
            return redirect()->route('dashboard')
                ->with('error', 'Acesso negado. Apenas parlamentares podem criar proposições.');
        }

        return $next($request);
    }
}
