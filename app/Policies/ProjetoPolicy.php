<?php

namespace App\Policies;

use App\Models\Projeto;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjetoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Projeto $projeto): bool
    {
        // Admin pode ver todos
        if ($user->isAdmin()) {
            return true;
        }

        // Parlamentar pode ver seus próprios projetos e os já votados
        if ($user->isParlamentar()) {
            return $projeto->autor_id === $user->id || 
                   in_array($projeto->status, ['aprovado', 'rejeitado', 'arquivado']);
        }

        // Outros usuários só podem ver projetos já votados
        return in_array($projeto->status, ['aprovado', 'rejeitado', 'arquivado']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isParlamentar() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Projeto $projeto): bool
    {
        // Admin pode editar todos
        if ($user->isAdmin()) {
            return true;
        }

        // Autor pode editar seus próprios projetos
        if ($projeto->autor_id === $user->id) {
            return true;
        }

        // Relator pode editar projetos que está relatando
        if ($projeto->relator_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Projeto $projeto): bool
    {
        // Admin pode excluir todos
        if ($user->isAdmin()) {
            return true;
        }

        // Autor pode excluir apenas seus próprios projetos em rascunho
        return $projeto->autor_id === $user->id && $projeto->status === 'rascunho';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Projeto $projeto): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Projeto $projeto): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can protocol the model.
     */
    public function protocol(User $user, Projeto $projeto): bool
    {
        return $user->isAdmin() || $projeto->autor_id === $user->id;
    }

    /**
     * Determine whether the user can edit content.
     */
    public function editContent(User $user, Projeto $projeto): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($projeto->autor_id === $user->id || $projeto->relator_id === $user->id) {
            return $projeto->podeEditarConteudo();
        }

        return false;
    }

    /**
     * Determine whether the user can add attachments.
     */
    public function addAttachment(User $user, Projeto $projeto): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($projeto->autor_id === $user->id || $projeto->relator_id === $user->id) {
            return $projeto->podeAnexarArquivos();
        }

        return false;
    }
}