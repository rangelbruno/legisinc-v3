<?php

namespace App\Policies;

use App\Models\Proposicao;
use App\Models\User;

class ProposicaoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admins, legislativo, protocolo e parlamentares podem ver proposições
        return $user->hasRole([
            User::PERFIL_ADMIN,
            User::PERFIL_LEGISLATIVO,
            User::PERFIL_PROTOCOLO,
            User::PERFIL_PARLAMENTAR,
            User::PERFIL_RELATOR
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Proposicao $proposicao): bool
    {
        // Admin pode ver todas
        if ($user->hasRole(User::PERFIL_ADMIN)) {
            return true;
        }

        // Legislativo e protocolo podem ver todas
        if ($user->hasRole([User::PERFIL_LEGISLATIVO, User::PERFIL_PROTOCOLO])) {
            return true;
        }

        // Parlamentar pode ver apenas suas próprias proposições
        if ($user->hasRole(User::PERFIL_PARLAMENTAR)) {
            return $proposicao->autor_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Apenas parlamentares podem criar proposições
        return $user->hasRole(User::PERFIL_PARLAMENTAR) && 
               $user->parlamentar && 
               $user->parlamentar->status === 'ativo';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Proposicao $proposicao): bool
    {
        // Admin pode editar todas
        if ($user->hasRole(User::PERFIL_ADMIN)) {
            return true;
        }

        // Legislativo pode editar proposições em análise
        if ($user->hasRole(User::PERFIL_LEGISLATIVO)) {
            return in_array($proposicao->status, ['EM_ANALISE', 'DEVOLVIDA']);
        }

        // Parlamentar pode editar apenas suas próprias proposições em rascunho
        if ($user->hasRole(User::PERFIL_PARLAMENTAR)) {
            return $proposicao->autor_id === $user->id && 
                   $proposicao->status === 'RASCUNHO';
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Proposicao $proposicao): bool
    {
        // Apenas admin ou parlamentar autor pode deletar proposição em rascunho
        if ($user->hasRole(User::PERFIL_ADMIN)) {
            return true;
        }

        if ($user->hasRole(User::PERFIL_PARLAMENTAR)) {
            return $proposicao->autor_id === $user->id && 
                   $proposicao->status === 'RASCUNHO';
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Proposicao $proposicao): bool
    {
        // Apenas admin pode restaurar
        return $user->hasRole(User::PERFIL_ADMIN);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Proposicao $proposicao): bool
    {
        // Apenas admin pode deletar permanentemente  
        return $user->hasRole(User::PERFIL_ADMIN);
    }
}
