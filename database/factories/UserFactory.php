<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * 👇 FIX: se agregó 'role' => 'recepcion' como valor por defecto.
     * Desde que role_id es NOT NULL en la tabla users (migración de
     * rediseño de roles), cualquier User::factory()->create() sin
     * especificar rol fallaba con "NOT NULL constraint failed:
     * users.role_id" — esto afectaba a TODOS los tests heredados de
     * Laravel Breeze (login, registro, perfil, etc.), que no tienen
     * por qué preocuparse de roles para probar autenticación básica.
     *
     * Los tests que sí necesitan un rol específico lo siguen pudiendo
     * sobrescribir normalmente: User::factory()->create(['role' => 'medico']).
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'recepcion',
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}