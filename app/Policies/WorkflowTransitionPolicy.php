<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkflowTransicao;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkflowTransitionPolicy
{
    use HandlesAuthorization;

    /**
     * Verifica se o usuário pode executar uma transição específica
     */
    public function executar(User $user, WorkflowTransicao $transicao, Model $documento): bool
    {
        // Administradores sempre podem executar transições
        if ($user->hasRole('admin')) {
            return true;
        }

        // Verificar se o usuário é o autor do documento (quando aplicável)
        if ($this->isAutorDocumento($user, $documento)) {
            return $this->podeAutorExecutarTransicao($transicao);
        }

        // Verificar permissões baseadas em role para a ação da transição
        return $this->verificarPermissaoPorRole($user, $transicao);
    }

    /**
     * Verifica se o usuário pode visualizar transições disponíveis
     */
    public function visualizar(User $user, Model $documento): bool
    {
        // Administradores sempre podem visualizar
        if ($user->hasRole('admin')) {
            return true;
        }

        // Autor do documento sempre pode visualizar suas transições
        if ($this->isAutorDocumento($user, $documento)) {
            return true;
        }

        // Usuários com roles específicas podem visualizar
        $rolesComAcesso = ['legislativo', 'protocolo', 'presidencia', 'juridico', 'expediente'];
        
        return $user->hasAnyRole($rolesComAcesso);
    }

    /**
     * Verifica se o usuário pode criar transições (admin)
     */
    public function criar(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Verifica se o usuário pode editar transições (admin)
     */
    public function editar(User $user, WorkflowTransicao $transicao): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Verifica se o usuário pode deletar transições (admin)
     */
    public function deletar(User $user, WorkflowTransicao $transicao): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Verifica se o usuário é o autor do documento
     */
    protected function isAutorDocumento(User $user, Model $documento): bool
    {
        // Verificar diferentes possibilidades de atributo de autor
        $camposAutor = ['autor_id', 'user_id', 'criado_por'];
        
        foreach ($camposAutor as $campo) {
            if ($documento->hasAttribute($campo) && $documento->$campo === $user->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica se o autor do documento pode executar a transição
     */
    protected function podeAutorExecutarTransicao(WorkflowTransicao $transicao): bool
    {
        // Ações que autores podem executar
        $acoesPermitidas = [
            'criar',
            'editar',
            'salvar',
            'enviar_protocolo',
            'retirar',
            'solicitar_alteracao_response'
        ];

        return in_array($transicao->acao, $acoesPermitidas);
    }

    /**
     * Verifica permissões baseadas em role para ação específica
     */
    protected function verificarPermissaoPorRole(User $user, WorkflowTransicao $transicao): bool
    {
        // Mapeamento detalhado de roles para ações
        $permissoesPorRole = [
            'parlamentar' => [
                'criar', 'editar', 'salvar', 'enviar_protocolo', 'retirar',
                'solicitar_alteracao_response', 'aprovar_autor'
            ],
            
            'protocolo' => [
                'protocolar', 'numerar', 'arquivar', 'desarquivar',
                'encaminhar_legislativo', 'devolver_autor'
            ],
            
            'legislativo' => [
                'analisar', 'aprovar_legislativo', 'rejeitar', 
                'solicitar_alteracao', 'encaminhar_presidencia',
                'solicitar_parecer_juridico'
            ],
            
            'presidencia' => [
                'aprovar_presidencia', 'rejeitar_presidencia',
                'encaminhar_votacao', 'encaminhar_legislativo'
            ],
            
            'juridico' => [
                'analisar_juridico', 'parecer_juridico_favoravel',
                'parecer_juridico_desfavoravel', 'solicitar_esclarecimentos'
            ],
            
            'expediente' => [
                'publicar', 'divulgar', 'notificar',
                'agendar_votacao', 'registrar_votacao'
            ]
        ];

        foreach ($user->roles as $role) {
            $acoesPermitidas = $permissoesPorRole[$role->name] ?? [];
            
            if (in_array($transicao->acao, $acoesPermitidas)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica se o usuário pode executar transições automáticas
     */
    public function executarAutomatica(User $user, WorkflowTransicao $transicao): bool
    {
        // Apenas sistema ou admin podem executar transições automáticas
        return $user->hasRole('admin') || $user->hasRole('sistema');
    }

    /**
     * Verifica permissões contextuais baseadas no estado do documento
     */
    public function executarComContexto(
        User $user, 
        WorkflowTransicao $transicao, 
        Model $documento,
        array $contexto = []
    ): bool {
        // Verificação base
        if (!$this->executar($user, $transicao, $documento)) {
            return false;
        }

        // Verificações contextuais específicas
        return $this->verificarContextoEspecifico($user, $transicao, $documento, $contexto);
    }

    /**
     * Verificações contextuais específicas
     */
    protected function verificarContextoEspecifico(
        User $user,
        WorkflowTransicao $transicao,
        Model $documento,
        array $contexto
    ): bool {
        // Regras específicas por tipo de documento
        if (method_exists($documento, 'getTipoDocumento')) {
            return $this->verificarPorTipoDocumento($user, $transicao, $documento->getTipoDocumento(), $contexto);
        }

        // Verificações por prioridade/urgência
        if (isset($contexto['urgente']) && $contexto['urgente']) {
            return $this->podeProcessarUrgente($user, $transicao);
        }

        return true;
    }

    /**
     * Verifica permissões específicas por tipo de documento
     */
    protected function verificarPorTipoDocumento(
        User $user,
        WorkflowTransicao $transicao,
        string $tipoDocumento,
        array $contexto
    ): bool {
        // Regras específicas podem ser implementadas aqui
        // Por exemplo: projetos de lei requerem análise jurídica obrigatória
        
        return true; // Por padrão, permitir
    }

    /**
     * Verifica se pode processar documentos urgentes
     */
    protected function podeProcessarUrgente(User $user, WorkflowTransicao $transicao): bool
    {
        $rolesUrgencia = ['admin', 'presidencia', 'legislativo'];
        
        return $user->hasAnyRole($rolesUrgencia);
    }
}