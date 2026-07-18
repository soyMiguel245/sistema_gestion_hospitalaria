<?php

namespace App\Policies;

use App\Models\User;
use App\Models\HistoriaClinica;

class HistoriaClinicaPolicy
{
    /**
     * IMPORTANTE: a diferencia de Paciente y Cita, aquí NO incluimos
     * 'recepcion'. El personal de recepción no debe ver el contenido
     * clínico de las historias, solo datos administrativos del paciente.
     * Esto es justo lo que el RNF-01 de tu propio documento exige.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['administrador', 'medico']);
    }

    public function view(User $user, HistoriaClinica $historia): bool
    {
        return $user->hasRole(['administrador', 'medico']);
    }

    /**
     * Solo un médico puede crear una historia clínica (es quien atiende).
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['administrador', 'medico']);
    }

    public function update(User $user, HistoriaClinica $historia): bool
    {
        return $user->hasRole(['administrador', 'medico']);
    }

    /**
     * Nadie debería poder borrar una historia clínica — por ley, los
     * registros médicos deben conservarse. Ni siquiera el Administrador.
     */
    public function delete(User $user, HistoriaClinica $historia): bool
    {
        return false;
    }
}