<?php

namespace Database\Factories;

use App\Models\AtencionMedica;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiagnosticoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'atencion_medica_id' => AtencionMedica::factory(),
            'descripcion' => fake()->sentence(3),
            'tipo' => 'Secundario',
            'cie10' => fake()->bothify('?##.#'),
            'observaciones' => null,
        ];
    }
}