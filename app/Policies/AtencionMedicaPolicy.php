<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AtencionMedica;

class AtencionMedicaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['administrador', 'medico']);
    }

    public function view(User $user, AtencionMedica $atencion): bool
    {
        return $user->hasRole(['administrador', 'medico']);
    }

    /**
     * Solo un médico registra una atención (es quien atendió al paciente).
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['administrador', 'medico']);
    }

    public function update(User $user, AtencionMedica $atencion): bool
    {
        return $user->hasRole(['administrador', 'medico']);
    }

    public function delete(User $user, AtencionMedica $atencion): bool
    {
        return $user->hasRole('administrador');
    }
}