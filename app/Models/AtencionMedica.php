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
        // 👇 examenes_adjuntos e imagenes_medicas se quitaron de aquí:
        // ahora viven en la tabla archivos_medicos, no en columnas JSON.
        'tipo_paciente',
        'costo',
        'descuento',
        'estado_pago',
        'numero_autorizacion',
        'estado',
        'proxima_cita',
        'alta_medica'
    ];

    protected $casts = [
        'alta_medica' => 'boolean',
        'proxima_cita' => 'datetime',
        'costo' => 'decimal:2',
        'descuento' => 'decimal:2',
        'temperatura' => 'decimal:1',
        'peso' => 'decimal:2',
        'talla' => 'decimal:2',
        'imc' => 'decimal:2',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function cita()
    {
        return $this->belongsTo(Cita::class)->withDefault([
            'fecha_hora' => null
        ]);
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'medico_id');
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function diagnosticos()
    {
        return $this->hasMany(Diagnostico::class, 'atencion_medica_id');
    }

    /**
     * 👇 NUEVO: reemplaza a los campos JSON examenes_adjuntos/imagenes_medicas.
     */
    public function archivos()
    {
        return $this->hasMany(ArchivoMedico::class, 'atencion_medica_id');
    }

    public function examenes()
    {
        return $this->archivos()->where('tipo', 'examen');
    }

    public function imagenesMedicas()
    {
        return $this->archivos()->where('tipo', 'imagen');
    }
}