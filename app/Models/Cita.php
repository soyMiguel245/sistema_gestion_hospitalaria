<?php

namespace App\Models;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo_cita',
        'paciente_id',
        'medico_id',
        'especialidad_id', // corregido
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

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medico()
    {
        return $this->belongsTo(User::class, 'medico_id');
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'especialidad_id');
    }
}
