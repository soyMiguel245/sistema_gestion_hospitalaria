<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Cita;

class CitaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['administrador', 'medico', 'recepcion']);
    }

    public function view(User $user, Cita $cita): bool
    {
        return $user->hasRole(['administrador', 'medico', 'recepcion']);
    }

    /**
     * Agendar citas es tarea de Recepción y Administrador.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['administrador', 'recepcion']);
    }

    public function update(User $user, Cita $cita): bool
    {
        return $user->hasRole(['administrador', 'recepcion']);
    }

    public function delete(User $user, Cita $cita): bool
    {
        return $user->hasRole(['administrador', 'recepcion']);
    }
}