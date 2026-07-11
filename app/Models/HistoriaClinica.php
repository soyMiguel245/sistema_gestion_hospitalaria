<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriaClinica extends Model
{
    use HasFactory;

    protected $table = 'historias_clinicas';

    protected $fillable = [
        'paciente_id',
        'cita_id',
        'medico_id',
        'registrado_por',
        'motivo_consulta',
        'antecedentes_personales',
        'antecedentes_familiares',
        'enfermedad_actual',
        'examen_fisico',
        'presion_arterial',
        'frecuencia_cardiaca',
        'frecuencia_respiratoria',
        'temperatura',
        'saturacion_o2',
        'peso',
        'talla',
        'imc',
        'diagnostico_principal',
        'diagnosticos_secundarios',
        'cie10',
        'tratamiento',
        'indicaciones',
        'procedimientos',
        'examenes',
        'evolucion',
        'proxima_cita',
        'alta_medica',
        'estado'
    ];

    // 🔗 RELACIONES
    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }

    public function medico()
    {
        return $this->belongsTo(User::class, 'medico_id');
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
