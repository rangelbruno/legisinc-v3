<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SessaoPlenaria;
use App\Models\Proposicao;

class ExpedientePolicy
{
    /**
     * Determina se o usuário pode visualizar qualquer sessão/expediente
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['EXPEDIENTE', 'ADMINISTRADOR']);
    }

    /**
     * Determina se o usuário pode visualizar uma sessão específica
     */
    public function view(User $user, ?SessaoPlenaria $sessao = null): bool
    {
        return $user->hasAnyRole(['EXPEDIENTE', 'ADMINISTRADOR', 'LEGISLATIVO']);
    }

    /**
     * Determina se o usuário pode criar sessões
     */
    public function create(User $user): bool
    {
        return $user->hasRole('EXPEDIENTE') || $user->hasRole('ADMINISTRADOR');
    }

    /**
     * Determina se o usuário pode editar uma sessão
     */
    public function update(User $user, SessaoPlenaria $sessao): bool
    {
        // Expediente pode editar se for criador ou admin
        if ($user->hasRole('ADMINISTRADOR')) {
            return true;
        }

        if ($user->hasRole('EXPEDIENTE')) {
            return $sessao->criado_por === $user->id || $sessao->status === 'AGENDADA';
        }

        return false;
    }

    /**
     * Determina se o usuário pode excluir uma sessão
     */
    public function delete(User $user, SessaoPlenaria $sessao): bool
    {
        // Só pode excluir se for admin ou criador da sessão agendada
        if ($user->hasRole('ADMINISTRADOR')) {
            return true;
        }

        return $user->hasRole('EXPEDIENTE') 
            && $sessao->criado_por === $user->id 
            && $sessao->status === 'AGENDADA';
    }

    /**
     * Determina se o usuário pode organizar pauta
     */
    public function organizarPauta(User $user): bool
    {
        return $user->hasAnyRole(['EXPEDIENTE', 'ADMINISTRADOR']);
    }

    /**
     * Determina se o usuário pode preparar votação
     */
    public function prepararVotacao(User $user): bool
    {
        return $user->hasAnyRole(['EXPEDIENTE', 'ADMINISTRADOR']);
    }

    /**
     * Determina se o usuário pode adicionar proposição à pauta
     */
    public function adicionarProposicaoPauta(User $user, Proposicao $proposicao): bool
    {
        if (!$user->hasAnyRole(['EXPEDIENTE', 'ADMINISTRADOR'])) {
            return false;
        }

        // Proposição deve estar protocolada
        return $proposicao->status === 'PROTOCOLADO';
    }

    /**
     * Determina se o usuário pode remover proposição da pauta
     */
    public function removerProposicaoPauta(User $user): bool
    {
        return $user->hasAnyRole(['EXPEDIENTE', 'ADMINISTRADOR']);
    }

    /**
     * Determina se o usuário pode iniciar sessão
     */
    public function iniciarSessao(User $user, SessaoPlenaria $sessao): bool
    {
        return $user->hasAnyRole(['EXPEDIENTE', 'ADMINISTRADOR'])
            && $sessao->status === 'AGENDADA';
    }

    /**
     * Determina se o usuário pode finalizar sessão
     */
    public function finalizarSessao(User $user, SessaoPlenaria $sessao): bool
    {
        return $user->hasAnyRole(['EXPEDIENTE', 'ADMINISTRADOR'])
            && $sessao->status === 'EM_ANDAMENTO';
    }

    /**
     * Determina se o usuário pode cancelar sessão
     */
    public function cancelarSessao(User $user, SessaoPlenaria $sessao): bool
    {
        return $user->hasAnyRole(['EXPEDIENTE', 'ADMINISTRADOR'])
            && in_array($sessao->status, ['AGENDADA', 'EM_ANDAMENTO']);
    }

    /**
     * Determina se o usuário pode gerar relatórios de sessão
     */
    public function gerarRelatorios(User $user): bool
    {
        return $user->hasAnyRole(['EXPEDIENTE', 'ADMINISTRADOR', 'LEGISLATIVO']);
    }
}