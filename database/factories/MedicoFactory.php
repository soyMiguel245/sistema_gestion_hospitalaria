<?php

namespace Database\Factories;

use App\Models\Especialidad;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombres' => fake()->firstName(),
            'apellidos' => fake()->lastName() . ' ' . fake()->lastName(),
            'dni' => fake()->unique()->numerify('########'),
            'cmp' => 'CMP' . fake()->unique()->numerify('#####'),
            'especialidad_id' => Especialidad::factory(),
            'estado' => 1,
        ];
    }
}