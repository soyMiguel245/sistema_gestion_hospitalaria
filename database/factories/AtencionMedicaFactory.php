<?php

namespace Database\Factories;

use App\Models\Medico;
use App\Models\Paciente;
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
            'registrado_por' => 1, // sobrescribible desde el test
        ];
    }
}