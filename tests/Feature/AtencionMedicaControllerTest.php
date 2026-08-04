<?php

namespace Tests\Feature;

use App\Models\AtencionMedica;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Cobertura del controlador con la lógica más delicada del sistema:
 * transacción atómica al registrar una atención + sus archivos, y
 * control de acceso por rol (solo médico/administrador).
 */
class AtencionMedicaControllerTest extends TestCase
{
    use RefreshDatabase;

    private function datosBasicos(): array
    {
        $medico = Medico::factory()->create();
        $paciente = Paciente::factory()->create();

        return [
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'motivo_consulta' => 'Control de rutina',
            'diagnostico' => 'Paciente estable',
            'tipo_paciente' => 'Particular',
            'estado_pago' => 'Pendiente',
            'estado' => 'Atendido',
        ];
    }

    #[Test]
    public function un_medico_puede_registrar_una_atencion_medica(): void
    {
        $medicoUser = User::factory()->create(['role' => 'medico']);

        $response = $this->actingAs($medicoUser)
            ->post('/atenciones', $this->datosBasicos());

        $response->assertRedirect(route('atenciones.index'));
        $this->assertDatabaseCount('atenciones_medicas', 1);
    }

    #[Test]
    public function recepcion_no_puede_registrar_una_atencion_medica(): void
    {
        $recepcion = User::factory()->create(['role' => 'recepcion']);

        $response = $this->actingAs($recepcion)
            ->post('/atenciones', $this->datosBasicos());

        $response->assertForbidden();
        $this->assertDatabaseCount('atenciones_medicas', 0);
    }

    #[Test]
    public function recepcion_no_puede_ver_el_listado_de_atenciones(): void
    {
        $recepcion = User::factory()->create(['role' => 'recepcion']);

        $this->actingAs($recepcion)
            ->get('/atenciones')
            ->assertForbidden();
    }

    #[Test]
    public function administrador_si_puede_ver_el_listado_de_atenciones(): void
    {
        $admin = User::factory()->activo2FA()->create(['role' => 'administrador']);

        $this->actingAs($admin)
            ->get('/atenciones')
            ->assertOk();
    }

    #[Test]
    public function registrar_una_atencion_sin_archivos_no_falla_la_transaccion(): void
    {
        // Caso base de la transacción: sin archivos adjuntos, debe
        // completar igual (confirma que DB::transaction() no exige
        // archivos para funcionar, solo los envuelve si existen).
        $medicoUser = User::factory()->create(['role' => 'medico']);

        $this->actingAs($medicoUser)->post('/atenciones', $this->datosBasicos());

        $atencion = AtencionMedica::first();
        $this->assertNotNull($atencion);
        $this->assertEquals(0, $atencion->archivos()->count());
    }

    #[Test]
    public function solo_administrador_puede_eliminar_una_atencion(): void
    {
        // La Policy restringe delete() solo a administrador, ni siquiera
        // el médico que la registró puede eliminarla.
        $medicoUser = User::factory()->create(['role' => 'medico']);
        $atencion = AtencionMedica::factory()->create();

        $this->actingAs($medicoUser)
            ->delete("/atenciones/{$atencion->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('atenciones_medicas', ['id' => $atencion->id]);
    }

    #[Test]
    public function eliminar_una_atencion_como_administrador_funciona_correctamente(): void
    {
        $admin = User::factory()->activo2FA()->create(['role' => 'administrador']);
        $atencion = AtencionMedica::factory()->create();

        $this->actingAs($admin)
            ->delete("/atenciones/{$atencion->id}")
            ->assertRedirect(route('atenciones.index'));

        $this->assertDatabaseMissing('atenciones_medicas', ['id' => $atencion->id]);
    }
}
