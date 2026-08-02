<?php

namespace App\Policies;

use App\Models\AtencionMedica;
use App\Models\User;

class AtencionMedicaPolicy
{
    /**
     * 👇 CORREGIDO: se agrega 'enfermera' — necesita ver el registro
     * clínico para tener contexto, pero no puede crear/editar/eliminar
     * (ver nota más abajo: eso requeriría un endpoint dedicado de
     * signos vitales para separar sus permisos de los del médico).
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['administrador', 'medico', 'enfermera']);
    }

    public function view(User $user, AtencionMedica $atencion): bool
    {
        return $user->hasRole(['administrador', 'medico', 'enfermera']);
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
