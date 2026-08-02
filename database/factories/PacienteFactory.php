<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PacienteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'dni' => fake()->unique()->numerify('########'),
            'numero_historia_clinica' => 'HC-'.fake()->unique()->numerify('#########'),
            'nombres' => fake()->firstName(),
            'apellidos' => fake()->lastName().' '.fake()->lastName(),
            'fecha_nacimiento' => fake()->date('Y-m-d', '-18 years'),
            'sexo' => fake()->randomElement(['Masculino', 'Femenino', 'Otro']),
            'telefono' => fake()->numerify('9########'),
            'correo' => fake()->unique()->safeEmail(),
            'estado' => 'Activo',
            'fecha_registro' => now(),
        ];
    }
}
