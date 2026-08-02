<?php

namespace App\Policies;

use App\Models\Cita;
use App\Models\User;

class CitaPolicy
{
    /**
     * 👇 CORREGIDO: se agrega 'enfermera' — necesita ver la agenda
     * para saber a quién va a atender.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['administrador', 'medico', 'recepcion', 'enfermera']);
    }

    public function view(User $user, Cita $cita): bool
    {
        return $user->hasRole(['administrador', 'medico', 'recepcion', 'enfermera']);
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
