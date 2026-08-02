<?php

namespace App\Policies;

use App\Models\Diagnostico;
use App\Models\User;

/**
 * Mismo criterio que AtencionMedicaPolicy: un diagnóstico es dato clínico
 * sensible, derivado directamente de una atención médica. Recepción y
 * enfermera no deben verlo ni gestionarlo — solo médico y administrador.
 */
class DiagnosticoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['administrador', 'medico']);
    }

    public function view(User $user, Diagnostico $diagnostico): bool
    {
        return $user->hasRole(['administrador', 'medico']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['administrador', 'medico']);
    }

    public function update(User $user, Diagnostico $diagnostico): bool
    {
        return $user->hasRole(['administrador', 'medico']);
    }

    public function delete(User $user, Diagnostico $diagnostico): bool
    {
        return $user->hasRole('administrador');
    }
}
