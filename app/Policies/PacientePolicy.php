<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Paciente;

class PacientePolicy
{
    /**
     * Quién puede VER el listado de pacientes.
     * Recepción los registra, Médico los consulta, Administrador ve todo.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['administrador', 'medico', 'recepcion']);
    }

    /**
     * Quién puede ver el detalle de UN paciente específico.
     */
    public function view(User $user, Paciente $paciente): bool
    {
        return $user->hasRole(['administrador', 'medico', 'recepcion']);
    }

    /**
     * Quién puede registrar un paciente nuevo.
     * Solo Recepción y Administrador (un médico no debería estar registrando
     * pacientes nuevos, esa es tarea administrativa).
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['administrador', 'recepcion']);
    }

    /**
     * Quién puede editar los datos de un paciente.
     */
    public function update(User $user, Paciente $paciente): bool
    {
        return $user->hasRole(['administrador', 'recepcion']);
    }

    /**
     * Quién puede eliminar un paciente.
     * Solo Administrador — eliminar un paciente con historial médico
     * es una acción delicada.
     */
    public function delete(User $user, Paciente $paciente): bool
    {
        return $user->hasRole('administrador');
    }
}