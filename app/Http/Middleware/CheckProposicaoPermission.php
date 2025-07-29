<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProposicaoPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission = null): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Admin tem acesso a tudo
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Verificar permissões baseadas na rota
        $route = $request->route()->getName();
        
        switch ($route) {
            // Rotas do Parlamentar
            case 'proposicoes.criar':
            case 'proposicoes.salvar-rascunho':
            case 'proposicoes.preencher-modelo':
            case 'proposicoes.gerar-texto':
            case 'proposicoes.editar-texto':
            case 'proposicoes.salvar-texto':
            case 'proposicoes.enviar-legislativo':
            case 'proposicoes.minhas-proposicoes':
                return $this->checkParlamentarPermission($user, $next, $request);

            // Rotas do Legislativo
            case 'proposicoes.legislativo.index':
            case 'proposicoes.legislativo.editar':
            case 'proposicoes.legislativo.salvar-edicao':
            case 'proposicoes.legislativo.enviar-parlamentar':
            case 'proposicoes.revisar':
            case 'proposicoes.revisar.show':
            case 'proposicoes.salvar-analise':
            case 'proposicoes.aprovar':
            case 'proposicoes.devolver':
            case 'proposicoes.relatorio-legislativo':
            case 'proposicoes.aguardando-protocolo':
            case 'proposicoes.onlyoffice.editor':
            case 'proposicoes.onlyoffice.download':
                return $this->checkLegislativoPermission($user, $next, $request);

            // Rotas de Assinatura (Parlamentar)
            case 'proposicoes.assinatura':
            case 'proposicoes.assinar':
            case 'proposicoes.corrigir':
            case 'proposicoes.confirmar-leitura':
            case 'proposicoes.processar-assinatura':
            case 'proposicoes.enviar-protocolo':
            case 'proposicoes.salvar-correcoes':
            case 'proposicoes.reenviar-legislativo':
            case 'proposicoes.historico-assinaturas':
                return $this->checkParlamentarPermission($user, $next, $request);

            // Rotas do Protocolo
            case 'proposicoes.protocolar':
            case 'proposicoes.protocolar.show':
            case 'proposicoes.efetivar-protocolo':
            case 'proposicoes.protocolos-hoje':
            case 'proposicoes.estatisticas-protocolo':
            case 'proposicoes.iniciar-tramitacao':
                return $this->checkProtocoloPermission($user, $next, $request);

            // Rotas gerais (todos os perfis autenticados)
            case 'proposicoes.show':
            case 'proposicoes.buscar-modelos':
                return $next($request);
            
            // Rota de callback do OnlyOffice (sem autenticação)
            case 'proposicoes.onlyoffice.callback':
                return $next($request);

            default:
                // Para outras rotas, verificar se é admin
                if ($user->isAdmin()) {
                    return $next($request);
                }
                
                abort(403, 'Acesso negado.');
        }
    }

    /**
     * Verificar permissões do Parlamentar
     */
    private function checkParlamentarPermission($user, $next, $request)
    {
        if ($user->isParlamentar() || $user->hasRole('PARLAMENTAR') || $user->isAdmin()) {
            return $next($request);
        }

        abort(403, 'Acesso restrito a parlamentares.');
    }

    /**
     * Verificar permissões do Legislativo
     */
    private function checkLegislativoPermission($user, $next, $request)
    {
        if ($user->hasRole('LEGISLATIVO') || $user->isAdmin()) {
            return $next($request);
        }

        abort(403, 'Acesso restrito ao setor legislativo.');
    }

    /**
     * Verificar permissões do Protocolo
     */
    private function checkProtocoloPermission($user, $next, $request)
    {
        if ($user->hasRole('PROTOCOLO') || $user->hasRole('LEGISLATIVO') || $user->isAdmin()) {
            return $next($request);
        }

        abort(403, 'Acesso restrito ao protocolo.');
    }
}