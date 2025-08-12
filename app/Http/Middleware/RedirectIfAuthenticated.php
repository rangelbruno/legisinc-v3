<?php

namespace App\Http\Middleware;

use App\Factories\NavigationControlFactory;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     * Redireciona usuários autenticados que tentam acessar páginas de login/registro
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Usuário está autenticado, redireciona para rota apropriada
                $redirectTo = NavigationControlFactory::getRedirectRoute();
                
                // Se retornou apenas "/" usa redirect direto, senão usa o redirect normal
                if ($redirectTo === '/') {
                    return redirect('/')
                        ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
                        ->header('Pragma', 'no-cache')
                        ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
                }
                
                // Adiciona headers para prevenir cache
                return redirect($redirectTo)
                    ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
            }
        }

        // Adiciona headers para prevenir cache nas páginas de autenticação
        $response = $next($request);
        
        if ($response instanceof Response) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        }
        
        return $response;
    }
}