<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Cita extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'codigo_cita',
        'paciente_id',
        'medico_id',
        'especialidad_id',
        'fecha_hora',
        'turno',
        'duracion',
        'consultorio',
        'tipo_cita',
        'origen',
        'area_servicio',
        'prioridad',
        'motivo',
        'motivo_clinico',
        'observaciones_medicas',
        'tipo_paciente',
        'costo',
        'estado_pago',
        'numero_autorizacion',
        'estado',
        'confirmada',
        'motivo_cancelacion',
        'motivo_reprogramacion',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'confirmada' => 'boolean',
        'costo' => 'decimal:2',
    ];

    /**
     * 👇 NUEVO: bitácora de auditoría para citas (quién agendó, canceló
     * o reprogramó, y cuándo).
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logExcept(['updated_at'])
            ->useLogName('cita');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'medico_id');
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'especialidad_id');
    }
}
