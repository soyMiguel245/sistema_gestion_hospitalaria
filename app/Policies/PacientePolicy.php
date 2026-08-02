<?php

namespace App\Policies;

use App\Models\Paciente;
use App\Models\User;

class PacientePolicy
{
    /**
     * 👇 CORREGIDO: se agrega 'enfermera' — necesita ver pacientes para
     * hacer su trabajo, pero solo lectura (ver más abajo en create/update/delete).
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['administrador', 'medico', 'recepcion', 'enfermera']);
    }

    public function view(User $user, Paciente $paciente): bool
    {
        return $user->hasRole(['administrador', 'medico', 'recepcion', 'enfermera']);
    }

    /**
     * Solo Recepción y Administrador. Enfermera NO crea pacientes
     * (es tarea administrativa).
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['administrador', 'recepcion']);
    }

    public function update(User $user, Paciente $paciente): bool
    {
        return $user->hasRole(['administrador', 'recepcion']);
    }

    public function delete(User $user, Paciente $paciente): bool
    {
        return $user->hasRole('administrador');
    }
}
