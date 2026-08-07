<?php

namespace Database\Factories;

use App\Models\Medico;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AtencionMedicaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'paciente_id' => Paciente::factory(),
            'medico_id' => Medico::factory(),
            'motivo_consulta' => fake()->sentence(4),
            'diagnostico' => fake()->sentence(3),
            'tipo_paciente' => 'Particular',
            'estado_pago' => 'Pendiente',
            'estado' => 'Atendido',
            // 👇 CORREGIDO: antes era un id=1 fijo, que no existe en tests
            // unitarios frescos sin seeders (RefreshDatabase). Usa el
            // factory de User, igual que paciente_id y medico_id — cada
            // atención creada trae su propio usuario "registrador",
            // salvo que el test lo sobrescriba explícitamente.
            'registrado_por' => User::factory(),
        ];
    }
}