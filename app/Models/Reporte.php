<?php

// app/Models/Reporte.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo',
        'reporte',
        'usuario_id',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}
