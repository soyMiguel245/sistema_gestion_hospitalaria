<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nombres',
        'apellidos',
        'dni',
        'cmp',
        'especialidad_id',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class);
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    public function atencionesMedicas()
    {
        return $this->hasMany(AtencionMedica::class);
    }
}
