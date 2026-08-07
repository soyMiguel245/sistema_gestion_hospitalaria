<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Primero el administrador, para que el sistema tenga acceso desde el minuto uno
        $this->call(AdminUserSeeder::class);
        // Luego llenamos las especialidades
        $this->call(EspecialidadSeeder::class);
        // Luego creamos un médico
        $this->call(MedicoSeeder::class);
    }
}
