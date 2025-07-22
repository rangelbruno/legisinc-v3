<?php

namespace App\Policies;

use App\Models\Documento\DocumentoInstancia;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DocumentoInstanciaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['ADMIN', 'LEGISLATIVO', 'PARLAMENTAR', 'admin', 'legislativo', 'parlamentar']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DocumentoInstancia $documentoInstancia): bool
    {
        return $user->hasRole(['ADMIN', 'LEGISLATIVO', 'PARLAMENTAR', 'admin', 'legislativo', 'parlamentar']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['ADMIN', 'LEGISLATIVO', 'PARLAMENTAR', 'admin', 'legislativo', 'parlamentar']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DocumentoInstancia $documentoInstancia): bool
    {
        return $user->hasRole(['ADMIN', 'LEGISLATIVO', 'PARLAMENTAR', 'admin', 'legislativo', 'parlamentar']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DocumentoInstancia $documentoInstancia): bool
    {
        return $user->hasRole(['ADMIN', 'admin']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DocumentoInstancia $documentoInstancia): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DocumentoInstancia $documentoInstancia): bool
    {
        return false;
    }
}
