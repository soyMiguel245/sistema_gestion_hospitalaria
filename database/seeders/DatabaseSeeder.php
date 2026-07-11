<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Primero llenamos las especialidades
        $this->call(EspecialidadSeeder::class);

        // Luego creamos un médico
        $this->call(MedicoSeeder::class);
    }
}
