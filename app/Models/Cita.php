<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

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
        'motivo_reprogramacion'
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'confirmada' => 'boolean',
        'costo' => 'decimal:2',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    /**
     * 👇 CORREGIDO: antes decía belongsTo(User::class, 'medico_id'),
     * pero la migración de `citas` liga medico_id a la tabla `medicos`.
     * Esto causaba que se cargara un User random con el mismo ID
     * numérico que el médico correcto, en vez del médico real.
     */
    public function medico()
    {
        return $this->belongsTo(Medico::class, 'medico_id');
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'especialidad_id');
    }
}