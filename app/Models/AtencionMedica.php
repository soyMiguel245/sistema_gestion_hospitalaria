<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Paciente;
use App\Models\Cita;
use App\Models\Medico;
use App\Models\User;

class AtencionMedica extends Model
{
    use HasFactory;

    protected $table = 'atenciones_medicas';

    protected $fillable = [
        'paciente_id',
        'cita_id',
        'medico_id',
        'registrado_por',
        'motivo_consulta',
        'diagnostico',
        'tratamiento',
        'procedimientos',
        'indicaciones',
        'observaciones',
        'presion_arterial',
        'frecuencia_cardiaca',
        'frecuencia_respiratoria',
        'temperatura',
        'saturacion_o2',
        'peso',
        'talla',
        'imc',
        'examenes_adjuntos',
        'imagenes_medicas',
        'tipo_paciente',
        'costo',
        'descuento',
        'estado_pago',
        'numero_autorizacion',
        'estado',
        'proxima_cita',
        'alta_medica'
    ];

    // 🔴 Para SQL Server y arrays de archivos
    protected $casts = [
        'examenes_adjuntos' => 'array',
        'imagenes_medicas' => 'array',
        'alta_medica' => 'boolean',
    ];

    // Fechas
    protected $dates = [
        'proxima_cita',
        'created_at',
        'updated_at'
    ];

    // Relaciones
    public function paciente()
    {
        return $this->belongsTo(Paciente::class)->withDefault([
            'nombres' => '-',
            'apellidos' => ''
        ]);
    }

    public function cita()
    {
        return $this->belongsTo(Cita::class)->withDefault([
            'fecha_hora' => null
        ]);
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'medico_id')->withDefault([
            'nombres' => '-',
            'apellidos' => ''
        ]);
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por')->withDefault([
            'name' => '-'
        ]);
    }
}
