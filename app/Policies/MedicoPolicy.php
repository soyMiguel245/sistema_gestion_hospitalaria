<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Medico;

class MedicoPolicy
{
    /**
     * Todos necesitan poder ver el directorio de médicos (recepción para
     * agendar citas, enfermera/médico para saber quién atiende).
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['administrador', 'medico', 'recepcion', 'enfermera']);
    }

    public function view(User $user, Medico $medico): bool
    {
        return $user->hasRole(['administrador', 'medico', 'recepcion', 'enfermera']);
    }

    /**
     * Gestionar el catálogo de médicos (con su CMP y especialidad) es
     * exclusivo de Administrador — es información profesional sensible.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('administrador');
    }

    public function update(User $user, Medico $medico): bool
    {
        return $user->hasRole('administrador');
    }

    public function delete(User $user, Medico $medico): bool
    {
        return $user->hasRole('administrador');
    }
}