<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medico;

class MedicoSeeder extends Seeder
{
    public function run(): void
    {
        // Crear un médico de ejemplo
        Medico::create([
            'nombres' => 'Juan',
            'apellidos' => 'Pérez',
            'dni' => '12345678',
            'cmp' => 'CMP12345',
            'especialidad_id' => 1, // Asegúrate de que exista una especialidad con id=1
            'estado' => 1
        ]);
    }
}
