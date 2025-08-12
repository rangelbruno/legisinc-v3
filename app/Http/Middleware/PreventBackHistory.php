<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventBackHistory
{
    /**
     * Handle an incoming request.
     * Adiciona headers para prevenir navegação com botão voltar
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Headers para prevenir cache e navegação com botão voltar
        return $response->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
                       ->header('Pragma', 'no-cache')
                       ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT')
                       ->header('Cache-Control', 'post-check=0, pre-check=0', false)
                       ->header('Last-Modified', gmdate("D, d M Y H:i:s") . ' GMT');
    }
}