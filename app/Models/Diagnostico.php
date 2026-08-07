<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnostico extends Model
{
    use HasFactory;

    protected $table = 'diagnosticos';

    protected $fillable = [
        'atencion_medica_id',
        'descripcion',
        'tipo', // Principal o Secundario
        'cie10',
        'observaciones',
    ];

    protected $casts = [
        // Cifrado AES-256 (Crypt/APP_KEY)
        'descripcion' => 'encrypted',
        'observaciones' => 'encrypted',
    ];

    // Relaciones
    public function atencionMedica()
    {
        return $this->belongsTo(AtencionMedica::class, 'atencion_medica_id');
    }
}
