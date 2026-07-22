<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Especialidad;

class EspecialidadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['administrador', 'medico', 'recepcion', 'enfermera']);
    }

    public function view(User $user, Especialidad $especialidad): bool
    {
        return $user->hasRole(['administrador', 'medico', 'recepcion', 'enfermera']);
    }

    /**
     * Solo Administrador. El borrado en particular es delicado: si la
     * especialidad tiene médicos asociados, hoy la BD tenía cascadeOnDelete
     * (corregido aparte a restrictOnDelete), así que esto es doblemente
     * sensible — solo admin debe poder tocarlo.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('administrador');
    }

    public function update(User $user, Especialidad $especialidad): bool
    {
        return $user->hasRole('administrador');
    }

    public function delete(User $user, Especialidad $especialidad): bool
    {
        return $user->hasRole('administrador');
    }
}