<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EspecialidadSeeder extends Seeder
{
    public function run(): void
    {
        $especialidades = [
            // Básicas
            'Medicina General',
            'Pediatría',
            'Ginecología y Obstetricia',
            'Cirugía General',
            'Medicina Interna',
            'Emergencia / Urgencias',
            'Anestesiología',

            // Clínicas
            'Cardiología',
            'Neurología',
            'Neumología',
            'Gastroenterología',
            'Endocrinología',
            'Reumatología',
            'Nefrología',
            'Hematología',
            'Infectología',
            'Dermatología',

            // Quirúrgicas
            'Traumatología y Ortopedia',
            'Urología',
            'Otorrinolaringología',
            'Oftalmología',
            'Cirugía Plástica',
            'Cirugía Pediátrica',
            'Neurocirugía',

            // Diagnóstico
            'Radiología / Imagenología',
            'Patología',
            'Medicina Nuclear',
            'Laboratorio Clínico',

            // Salud mental
            'Psiquiatría',
            'Psicología Clínica',

            // Otras
            'Geriatría',
            'Medicina Familiar',
            'Medicina Física y Rehabilitación',
            'Oncología',
        ];

        foreach ($especialidades as $nombre) {
            DB::table('especialidades')->insert([
                'nombre' => $nombre,
                'estado' => 1,
            ]);
        }
    }
}
