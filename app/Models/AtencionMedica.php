<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'alta_medica',
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
        // Campos clínicos de texto libre — cifrado AES-256 (Crypt/APP_KEY)
        'motivo_consulta' => 'encrypted',
        'diagnostico' => 'encrypted',
        'tratamiento' => 'encrypted',
        'procedimientos' => 'encrypted',
        'indicaciones' => 'encrypted',
        'observaciones' => 'encrypted',
    ];

    /**
     * Bitácora de auditoría — esta es la tabla clínica más sensible del
     * sistema, así que queda registrado cada cambio con quién lo hizo y
     * cuándo.
     *
     * Los campos cifrados quedan fuera del log (logExcept): si no los
     * excluimos, Spatie registra el valor YA DESCIFRADO (post-cast) en
     * activity_log.properties, que no está cifrada, y se pierde el
     * propósito de cifrar la columna original.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logExcept([
                'updated_at',
                'motivo_consulta',
                'diagnostico',
                'tratamiento',
                'procedimientos',
                'indicaciones',
                'observaciones',
            ])
            ->useLogName('atencion_medica');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function cita()
    {
        return $this->belongsTo(Cita::class)->withDefault([
            'fecha_hora' => null,
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
