<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ArchivoMedico extends Model
{
    use HasFactory;

    protected $table = 'archivos_medicos';

    protected $fillable = [
        'atencion_medica_id',
        'tipo',
        'ruta',
        'nombre_original',
        'mime_type',
        'tamano_bytes',
        'subido_por',
    ];

    public function atencionMedica()
    {
        return $this->belongsTo(AtencionMedica::class, 'atencion_medica_id');
    }

    public function subidoPor()
    {
        return $this->belongsTo(User::class, 'subido_por');
    }

    /**
     * URL pública para mostrar o descargar el archivo desde el disco 'public'.
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->ruta);
    }
}