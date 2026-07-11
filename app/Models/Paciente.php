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
}
