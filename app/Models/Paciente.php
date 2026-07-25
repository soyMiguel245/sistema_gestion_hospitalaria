<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Paciente extends Model
{
    use HasFactory, LogsActivity;

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
    ];

    /**
     * 👇 NUEVO: registra en la bitácora quién creó/editó/eliminó un
     * paciente, y qué campos cambiaron. Solo se registran los campos
     * que de verdad cambiaron (logOnlyDirty), para no llenar la
     * bitácora de ruido.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logExcept(['updated_at'])
            ->useLogName('paciente');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    public function atencionesMedicas()
    {
        return $this->hasMany(AtencionMedica::class);
    }

    public function historialClinico()
    {
        return $this->atencionesMedicas()
            ->with(['diagnosticos', 'medico', 'cita'])
            ->orderByDesc('created_at');
    }
}