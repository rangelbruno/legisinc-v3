<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ParecerJuridico;
use App\Models\Proposicao;

class ParecerJuridicoPolicy
{
    /**
     * Determina se o usuário pode visualizar qualquer parecer
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['ASSESSOR_JURIDICO', 'ADMINISTRADOR', 'LEGISLATIVO', 'EXPEDIENTE']);
    }

    /**
     * Determina se o usuário pode visualizar um parecer específico
     */
    public function view(User $user, ParecerJuridico $parecer): bool
    {
        // Assessor pode ver seus próprios pareceres
        if ($user->hasRole('ASSESSOR_JURIDICO') && $parecer->assessor_id === $user->id) {
            return true;
        }

        // Admin, Legislativo e Expediente podem ver todos
        return $user->hasAnyRole(['ADMINISTRADOR', 'LEGISLATIVO', 'EXPEDIENTE']);
    }

    /**
     * Determina se o usuário pode criar pareceres
     */
    public function create(User $user): bool
    {
        return $user->hasRole('ASSESSOR_JURIDICO') || $user->hasRole('ADMINISTRADOR');
    }

    /**
     * Determina se o usuário pode criar parecer para uma proposição específica
     */
    public function createForProposicao(User $user, Proposicao $proposicao): bool
    {
        if (!$this->create($user)) {
            return false;
        }

        // Proposição deve estar protocolada e não pode já ter parecer
        return $proposicao->status === 'PROTOCOLADO' && !$proposicao->tem_parecer;
    }

    /**
     * Determina se o usuário pode editar um parecer
     */
    public function update(User $user, ParecerJuridico $parecer): bool
    {
        // Admin pode editar qualquer parecer
        if ($user->hasRole('ADMINISTRADOR')) {
            return true;
        }

        // Assessor só pode editar seus próprios pareceres
        return $user->hasRole('ASSESSOR_JURIDICO') && $parecer->assessor_id === $user->id;
    }

    /**
     * Determina se o usuário pode excluir um parecer
     */
    public function delete(User $user, ParecerJuridico $parecer): bool
    {
        // Apenas admin pode excluir pareceres
        if ($user->hasRole('ADMINISTRADOR')) {
            return true;
        }

        // Assessor pode excluir apenas se for seu próprio parecer e não estiver em uso
        if ($user->hasRole('ASSESSOR_JURIDICO') && $parecer->assessor_id === $user->id) {
            // Verificar se a proposição ainda não está em pauta
            return !$parecer->proposicao->estaEmPauta();
        }

        return false;
    }

    /**
     * Determina se o usuário pode baixar PDF do parecer
     */
    public function downloadPdf(User $user, ParecerJuridico $parecer): bool
    {
        return $this->view($user, $parecer);
    }

    /**
     * Determina se o usuário pode revisar pareceres de outros assessores
     */
    public function review(User $user, ParecerJuridico $parecer): bool
    {
        // Apenas admin e outros assessores jurídicos (não o autor)
        if ($user->hasRole('ADMINISTRADOR')) {
            return true;
        }

        return $user->hasRole('ASSESSOR_JURIDICO') && $parecer->assessor_id !== $user->id;
    }

    /**
     * Determina se o usuário pode gerar relatórios de pareceres
     */
    public function gerarRelatorios(User $user): bool
    {
        return $user->hasAnyRole(['ASSESSOR_JURIDICO', 'ADMINISTRADOR', 'LEGISLATIVO']);
    }

    /**
     * Determina se o usuário pode ver estatísticas de pareceres
     */
    public function viewEstatisticas(User $user): bool
    {
        return $user->hasAnyRole(['ASSESSOR_JURIDICO', 'ADMINISTRADOR', 'LEGISLATIVO']);
    }

    /**
     * Determina se o usuário pode solicitar parecer jurídico
     */
    public function solicitar(User $user, Proposicao $proposicao): bool
    {
        // Expediente e Legislativo podem solicitar pareceres
        if (!$user->hasAnyRole(['EXPEDIENTE', 'LEGISLATIVO', 'ADMINISTRADOR'])) {
            return false;
        }

        // Proposição deve estar protocolada e não ter parecer
        return $proposicao->status === 'PROTOCOLADO' && !$proposicao->tem_parecer;
    }

    /**
     * Determina se o usuário pode anexar documentos ao parecer
     */
    public function anexarDocumentos(User $user, ParecerJuridico $parecer): bool
    {
        // Admin pode anexar a qualquer parecer
        if ($user->hasRole('ADMINISTRADOR')) {
            return true;
        }

        // Assessor pode anexar apenas aos próprios pareceres
        return $user->hasRole('ASSESSOR_JURIDICO') && $parecer->assessor_id === $user->id;
    }

    /**
     * Determina se o usuário pode marcar parecer como urgente
     */
    public function marcarUrgente(User $user): bool
    {
        return $user->hasAnyRole(['ASSESSOR_JURIDICO', 'ADMINISTRADOR', 'LEGISLATIVO']);
    }
}