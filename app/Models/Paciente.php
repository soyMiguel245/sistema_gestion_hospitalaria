<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    protected $table = 'pacientes';

    protected $fillable = [
        'dni',
        'numero_historia_clinica',

        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'sexo',
        'estado_civil',
        'nacionalidad',

        'telefono',
        'correo',
        'direccion',

        'contacto_emergencia',
        'telefono_emergencia',

        'tipo_sangre',
        'alergias',
        'enfermedades_cronicas',
        'observaciones',

        'tipo_seguro',
        'estado',
        'fecha_registro'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_registro' => 'date',
        // 'estado' guarda 'Activo'/'Inactivo' como texto (confirmado por
        // el CHECK constraint de la BD), no es un booleano.
    ];

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    public function atencionesMedicas()
    {
        return $this->hasMany(AtencionMedica::class);
    }

    /**
     * La "historia clínica" del paciente NO es una tabla aparte: es la línea
     * de tiempo completa de sus atenciones médicas, cada una con sus
     * diagnósticos. Esto reemplaza al antiguo modelo HistoriaClinica,
     * que duplicaba estos mismos datos en otra tabla.
     *
     * Uso: $paciente->historialClinico() para el expediente completo,
     *      ordenado de la atención más reciente a la más antigua.
     */
    public function historialClinico()
    {
        return $this->atencionesMedicas()
            ->with(['diagnosticos', 'medico', 'cita'])
            ->orderByDesc('created_at');
    }
}