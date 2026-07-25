<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EspecialidadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->randomElement([
                'Medicina General', 'Pediatría', 'Cardiología', 'Traumatología',
                'Dermatología', 'Neurología', 'Ginecología', 'Oftalmología',
            ]) . ' ' . fake()->unique()->numerify('##'), // evita choque si se crean varias
            'estado' => 1,
        ];
    }
}