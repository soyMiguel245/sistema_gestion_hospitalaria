<?php

namespace Database\Factories;

use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Paciente;
use Illuminate\Database\Eloquent\Factories\Factory;

class CitaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'codigo_cita' => 'CITA-'.fake()->unique()->numerify('##########'),
            'paciente_id' => Paciente::factory(),
            'medico_id' => Medico::factory(),
            'especialidad_id' => Especialidad::factory(),
            'fecha_hora' => now()->addDays(fake()->numberBetween(1, 30))->setTime(fake()->numberBetween(8, 17), 0),
            'turno' => 'Mañana',
            'duracion' => 30,
            'tipo_cita' => 'Consulta',
            'origen' => 'Presencial',
            'area_servicio' => 'Consulta Externa',
            'prioridad' => 'Normal',
            'motivo' => fake()->sentence(4),
            'tipo_paciente' => 'Particular',
            'estado_pago' => 'Pendiente',
            'estado' => 'Programada',
            'confirmada' => false,
        ];
    }
}
