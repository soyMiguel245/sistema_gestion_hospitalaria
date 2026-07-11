<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombres',
        'apellidos',
        'dni',
        'cmp',
        'especialidad_id',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean'
    ];

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class);
    }
}
