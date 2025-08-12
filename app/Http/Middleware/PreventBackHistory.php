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
        
        // Verificar se é BinaryFileResponse (downloads) ou outras respostas especiais
        if ($response instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse ||
            $response instanceof \Symfony\Component\HttpFoundation\StreamedResponse) {
            // Para file downloads, não aplicar headers de cache - deixar como está
            return $response;
        }
        
        // Headers para prevenir cache e navegação com botão voltar
        // Aplicar apenas para responses que suportam o método header()
        if (method_exists($response, 'header')) {
            return $response->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
                           ->header('Pragma', 'no-cache')
                           ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT')
                           ->header('Cache-Control', 'post-check=0, pre-check=0', false)
                           ->header('Last-Modified', gmdate("D, d M Y H:i:s") . ' GMT');
        }
        
        // Para outros tipos de resposta, usar headers() diretamente
        $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        $response->headers->set('Last-Modified', gmdate("D, d M Y H:i:s") . ' GMT');
        
        return $response;
    }
}