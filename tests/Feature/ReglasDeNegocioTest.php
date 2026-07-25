<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Reglas de negocio críticas del sistema. A diferencia de RolePermissionsTest
 * (que prueba QUIÉN puede entrar), estos prueban que los DATOS del hospital
 * se mantengan consistentes sin importar quién los está manipulando.
 */
class ReglasDeNegocioTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'administrador']);
    }

    #[Test]
    public function no_se_puede_registrar_dos_pacientes_con_el_mismo_dni(): void
    {
        Paciente::factory()->create(['dni' => '12345678']);

        $response = $this->actingAs($this->admin())->post('/pacientes', [
            'dni' => '12345678', // <-- duplicado a propósito
            'nombres' => 'Juan',
            'apellidos' => 'Test Duplicado',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'Masculino',
        ]);

        $response->assertSessionHasErrors('dni');
        $this->assertEquals(1, Paciente::where('dni', '12345678')->count());
    }

    #[Test]
    public function no_se_puede_agendar_una_cita_que_choca_con_otra_del_mismo_medico(): void
    {
        $medico = Medico::factory()->create();
        $paciente1 = Paciente::factory()->create();
        $paciente2 = Paciente::factory()->create();

        $horario = now()->addDays(5)->setTime(10, 0);

        // Primera cita: 10:00 - 10:30 (duracion 30 min)
        Cita::factory()->create([
            'medico_id' => $medico->id,
            'paciente_id' => $paciente1->id,
            'fecha_hora' => $horario,
            'duracion' => 30,
            'estado' => 'Programada',
        ]);

        // Intento de segunda cita para el MISMO médico, 10:15 -> se solapa
        // con la primera (10:00-10:30), aunque no sea la misma hora exacta.
        $response = $this->actingAs($this->admin())->post('/citas', [
            'paciente_id' => $paciente2->id,
            'medico_id' => $medico->id,
            'especialidad_id' => $medico->especialidad_id,
            'fecha_hora' => $horario->copy()->addMinutes(15)->format('Y-m-d H:i:s'),
            'duracion' => 30,
            'turno' => 'Mañana',
            'tipo_cita' => 'Consulta',
            'origen' => 'Presencial',
            'area_servicio' => 'Consulta Externa',
            'prioridad' => 'Normal',
            'motivo' => 'Control',
            'tipo_paciente' => 'Particular',
            'estado' => 'Programada',
        ]);

        $response->assertSessionHasErrors('fecha_hora');
        // Solo debe existir la cita original, la segunda no se guardó.
        $this->assertEquals(1, Cita::where('medico_id', $medico->id)->count());
    }

    #[Test]
    public function si_se_agenda_fuera_del_rango_de_la_cita_existente_si_se_permite(): void
    {
        $medico = Medico::factory()->create();
        $paciente1 = Paciente::factory()->create();
        $paciente2 = Paciente::factory()->create();

        $horario = now()->addDays(5)->setTime(10, 0);

        Cita::factory()->create([
            'medico_id' => $medico->id,
            'paciente_id' => $paciente1->id,
            'fecha_hora' => $horario, // 10:00 - 10:30
            'duracion' => 30,
            'estado' => 'Programada',
        ]);

        // Esta cita empieza justo cuando termina la anterior (10:30) -> NO choca.
        $response = $this->actingAs($this->admin())->post('/citas', [
            'paciente_id' => $paciente2->id,
            'medico_id' => $medico->id,
            'especialidad_id' => $medico->especialidad_id,
            'fecha_hora' => $horario->copy()->addMinutes(30)->format('Y-m-d H:i:s'),
            'duracion' => 30,
            'turno' => 'Mañana',
            'tipo_cita' => 'Consulta',
            'origen' => 'Presencial',
            'area_servicio' => 'Consulta Externa',
            'prioridad' => 'Normal',
            'motivo' => 'Control',
            'tipo_paciente' => 'Particular',
            'estado' => 'Programada',
        ]);

        $response->assertSessionDoesntHaveErrors('fecha_hora');
        $this->assertEquals(2, Cita::where('medico_id', $medico->id)->count());
    }
}