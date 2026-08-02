<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     * 👇 CORREGIDO (hallazgo crítico de seguridad): antes esto devolvía una
     * URL pública directa (Storage::disk('public')->url(...)), accesible
     * por CUALQUIERA con el link, sin login ni permisos. Ahora el archivo
     * vive en el disco 'local' (privado, fuera de /public) y solo se sirve
     * a través de la ruta controlada archivos.descargar, que exige
     * autenticación + Policy antes de entregar el contenido.
     */
    public function rutaDescarga(): string
    {
        return route('archivos.descargar', $this->id);
    }
}
