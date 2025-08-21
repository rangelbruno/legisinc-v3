<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class CheckParlamentarAtivo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Se o usuário não está autenticado, redireciona para login
        if (!$user) {
            return redirect()->route('login')->with('error', 'Você precisa estar autenticado para acessar esta área.');
        }
        
        // Se o usuário não tem role de parlamentar, bloqueia acesso
        if (!$user->hasRole(User::PERFIL_PARLAMENTAR)) {
            abort(403, 'Acesso negado. Apenas parlamentares podem acessar esta área.');
        }
        
        // Verificar se o parlamentar está cadastrado e ativo
        $parlamentar = $user->parlamentar;
        
        if (!$parlamentar) {
            return redirect()->route('dashboard')->with('error', 'Seu usuário não está vinculado a um cadastro de parlamentar. Entre em contato com o administrador.');
        }
        
        // Verificar se o parlamentar está ativo
        if ($parlamentar->status !== 'ativo') {
            $mensagem = match($parlamentar->status) {
                'licenciado' => 'Seu cadastro de parlamentar está licenciado. Durante o período de licença, o acesso ao sistema é restrito.',
                'inativo' => 'Seu cadastro de parlamentar está inativo. Entre em contato com o administrador para reativar seu acesso.',
                default => 'Seu cadastro de parlamentar não está ativo no momento.'
            };
            
            return redirect()->route('dashboard')->with('warning', $mensagem);
        }
        
        // Se chegou aqui, o usuário é um parlamentar ativo
        // Adicionar informações do parlamentar ao request para uso posterior
        $request->merge(['parlamentar' => $parlamentar]);
        
        return $next($request);
    }
}