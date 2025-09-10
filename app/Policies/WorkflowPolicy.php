<?php

namespace App\Policies;

use App\Models\{User, Workflow, WorkflowEtapa, Proposicao};
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkflowPolicy
{
    use HandlesAuthorization;

    /**
     * Determina se o usuário pode visualizar workflows
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Legislativo']);
    }

    /**
     * Determina se o usuário pode visualizar um workflow específico
     */
    public function view(User $user, Workflow $workflow): bool
    {
        return $user->hasAnyRole(['Admin', 'Legislativo']);
    }

    /**
     * Determina se o usuário pode criar workflows
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determina se o usuário pode atualizar workflows
     */
    public function update(User $user, Workflow $workflow): bool
    {
        if (!$user->hasRole('Admin')) {
            return false;
        }

        // Admin não pode editar workflow se houver documentos em uso
        return !$workflow->temDocumentosEmUso();
    }

    /**
     * Determina se o usuário pode deletar workflows
     */
    public function delete(User $user, Workflow $workflow): bool
    {
        if (!$user->hasRole('Admin')) {
            return false;
        }

        // Admin não pode deletar workflow se houver documentos em uso ou histórico
        return !$workflow->temDocumentosEmUso() && !$workflow->historico()->exists();
    }

    /**
     * Determina se o usuário pode ativar/desativar workflows
     */
    public function toggle(User $user, Workflow $workflow): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determina se o usuário pode duplicar workflows
     */
    public function duplicate(User $user, Workflow $workflow): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determina se o usuário pode definir workflow como padrão
     */
    public function setDefault(User $user, Workflow $workflow): bool
    {
        return $user->hasRole('Admin');
    }

    // ==========================================
    // AÇÕES DE WORKFLOW (TRANSIÇÕES)
    // ==========================================

    /**
     * Pode aprovar na etapa legislativa
     */
    public function aprovar(User $user, $documento, WorkflowEtapa $etapa): bool
    {
        // Verificar role da etapa
        if ($etapa->role_responsavel && !$user->hasRole($etapa->role_responsavel)) {
            return false;
        }

        // Ação deve estar permitida na etapa
        if (!in_array('aprovar', $etapa->acoes_possiveis ?? [])) {
            return false;
        }

        // Específico para Legislativo
        if ($etapa->role_responsavel === 'Legislativo') {
            return $user->hasRole('Legislativo');
        }

        return true;
    }

    /**
     * Pode devolver documento
     */
    public function devolver(User $user, $documento, WorkflowEtapa $etapa): bool
    {
        if ($etapa->role_responsavel && !$user->hasRole($etapa->role_responsavel)) {
            return false;
        }

        if (!in_array('devolver', $etapa->acoes_possiveis ?? [])) {
            return false;
        }

        return $user->hasAnyRole(['Legislativo', 'Protocolo']);
    }

    /**
     * Pode solicitar alterações
     */
    public function solicitar_alteracoes(User $user, $documento, WorkflowEtapa $etapa): bool
    {
        if ($etapa->role_responsavel && !$user->hasRole($etapa->role_responsavel)) {
            return false;
        }

        if (!in_array('solicitar_alteracoes', $etapa->acoes_possiveis ?? [])) {
            return false;
        }

        return $user->hasRole('Legislativo');
    }

    /**
     * Pode assinar documento
     */
    public function assinar(User $user, $documento, WorkflowEtapa $etapa): bool
    {
        // Verificar role da etapa
        if ($etapa->role_responsavel && !$user->hasRole($etapa->role_responsavel)) {
            return false;
        }

        // Ação deve estar permitida na etapa
        if (!in_array('assinar', $etapa->acoes_possiveis ?? [])) {
            return false;
        }

        // Para proposições, verificar se é o autor
        if ($documento instanceof Proposicao) {
            return $documento->parlamentar->user_id === $user->id;
        }

        return $user->hasRole('Parlamentar');
    }

    /**
     * Pode protocolar documento
     */
    public function protocolar(User $user, $documento, WorkflowEtapa $etapa): bool
    {
        if ($etapa->role_responsavel && !$user->hasRole($etapa->role_responsavel)) {
            return false;
        }

        if (!in_array('protocolar', $etapa->acoes_possiveis ?? [])) {
            return false;
        }

        return $user->hasRole('Protocolo');
    }

    /**
     * Pode finalizar documento
     */
    public function finalizar(User $user, $documento, WorkflowEtapa $etapa): bool
    {
        if ($etapa->role_responsavel && !$user->hasRole($etapa->role_responsavel)) {
            return false;
        }

        if (!in_array('finalizar', $etapa->acoes_possiveis ?? [])) {
            return false;
        }

        return $user->hasRole('Expediente');
    }

    /**
     * Pode arquivar documento
     */
    public function arquivar(User $user, $documento, WorkflowEtapa $etapa): bool
    {
        if ($etapa->role_responsavel && !$user->hasRole($etapa->role_responsavel)) {
            return false;
        }

        if (!in_array('arquivar', $etapa->acoes_possiveis ?? [])) {
            return false;
        }

        return $user->hasAnyRole(['Expediente', 'Admin']);
    }

    /**
     * Pode enviar para legislativo
     */
    public function enviar_legislativo(User $user, $documento, WorkflowEtapa $etapa): bool
    {
        if ($etapa->role_responsavel && !$user->hasRole($etapa->role_responsavel)) {
            return false;
        }

        if (!in_array('enviar_legislativo', $etapa->acoes_possiveis ?? [])) {
            return false;
        }

        // Para proposições, verificar se é o autor
        if ($documento instanceof Proposicao) {
            return $documento->parlamentar->user_id === $user->id;
        }

        return $user->hasRole('Parlamentar');
    }

    /**
     * Pode enviar para protocolo (fluxo simplificado)
     */
    public function enviar_protocolo(User $user, $documento, WorkflowEtapa $etapa): bool
    {
        if ($etapa->role_responsavel && !$user->hasRole($etapa->role_responsavel)) {
            return false;
        }

        if (!in_array('enviar_protocolo', $etapa->acoes_possiveis ?? [])) {
            return false;
        }

        // Para proposições, verificar se é o autor
        if ($documento instanceof Proposicao) {
            return $documento->parlamentar->user_id === $user->id;
        }

        return $user->hasRole('Parlamentar');
    }

    /**
     * Pode devolver para edição
     */
    public function devolver_edicao(User $user, $documento, WorkflowEtapa $etapa): bool
    {
        if ($etapa->role_responsavel && !$user->hasRole($etapa->role_responsavel)) {
            return false;
        }

        if (!in_array('devolver_edicao', $etapa->acoes_possiveis ?? [])) {
            return false;
        }

        // Para proposições, verificar se é o autor
        if ($documento instanceof Proposicao) {
            return $documento->parlamentar->user_id === $user->id;
        }

        return $user->hasRole('Parlamentar');
    }

    /**
     * Pode salvar como rascunho
     */
    public function salvar_rascunho(User $user, $documento, WorkflowEtapa $etapa): bool
    {
        if ($etapa->role_responsavel && !$user->hasRole($etapa->role_responsavel)) {
            return false;
        }

        if (!in_array('salvar_rascunho', $etapa->acoes_possiveis ?? [])) {
            return false;
        }

        // Para proposições, verificar se é o autor
        if ($documento instanceof Proposicao) {
            return $documento->parlamentar->user_id === $user->id;
        }

        return $user->hasRole('Parlamentar');
    }

    // ==========================================
    // MÉTODOS AUXILIARES
    // ==========================================

    /**
     * Verifica se usuário pode pausar workflow
     */
    public function pause(User $user, $documento): bool
    {
        return $user->hasAnyRole(['Admin', 'Legislativo']);
    }

    /**
     * Verifica se usuário pode retomar workflow
     */
    public function resume(User $user, $documento): bool
    {
        return $user->hasAnyRole(['Admin', 'Legislativo']);
    }

    /**
     * Verifica se usuário pode ver histórico do workflow
     */
    public function viewHistory(User $user, $documento): bool
    {
        // Parlamentar só vê próprias proposições
        if ($documento instanceof Proposicao && $user->hasRole('Parlamentar')) {
            return $documento->parlamentar->user_id === $user->id;
        }

        // Outros roles podem ver todos os históricos
        return $user->hasAnyRole(['Admin', 'Legislativo', 'Protocolo', 'Expediente']);
    }

    /**
     * Verifica se usuário pode ver status do workflow
     */
    public function viewStatus(User $user, $documento): bool
    {
        // Parlamentar só vê próprias proposições
        if ($documento instanceof Proposicao && $user->hasRole('Parlamentar')) {
            return $documento->parlamentar->user_id === $user->id;
        }

        // Outros roles podem ver todos os status
        return $user->hasAnyRole(['Admin', 'Legislativo', 'Protocolo', 'Expediente']);
    }
}