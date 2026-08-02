<?php

namespace Tests\Feature;

use App\Models\AtencionMedica;
use App\Models\Cita;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

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
            'dni' => '12345678',
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

        Cita::factory()->create([
            'medico_id' => $medico->id,
            'paciente_id' => $paciente1->id,
            'fecha_hora' => $horario,
            'duracion' => 30,
            'estado' => 'Programada',
        ]);

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
            'fecha_hora' => $horario,
            'duracion' => 30,
            'estado' => 'Programada',
        ]);

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

    /**
     * 👇 NUEVO: confirma en automático lo que verificamos a mano en el
     * navegador — que ver el expediente completo de un paciente queda
     * registrado en la Bitácora, cumpliendo el RNF-01 ("cada acceso
     * queda registrado", no solo cada modificación).
     */
    #[Test]
    public function ver_el_historial_de_un_paciente_queda_registrado_en_la_bitacora(): void
    {
        $medicoUser = User::factory()->create(['role' => 'medico']);
        $paciente = Paciente::factory()->create();
        $medicoModel = Medico::factory()->create();

        AtencionMedica::factory()->create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medicoModel->id,
            'registrado_por' => $medicoUser->id,
        ]);

        $this->actingAs($medicoUser)->get("/historias/{$paciente->id}");

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'historial_clinico',
            'subject_id' => $paciente->id,
            'subject_type' => Paciente::class,
            'causer_id' => $medicoUser->id,
        ]);
    }
}
