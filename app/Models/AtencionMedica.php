<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Paciente;
use App\Models\Cita;
use App\Models\Medico;
use App\Models\User;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AtencionMedica extends Model
{
    use HasFactory, LogsActivity;

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

    /**
     * 👇 NUEVO: bitácora de auditoría — esta es la tabla clínica más
     * sensible del sistema (diagnóstico, tratamiento), así que queda
     * registrado cada cambio con quién lo hizo y cuándo.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logExcept(['updated_at'])
            ->useLogName('atencion_medica');
    }

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