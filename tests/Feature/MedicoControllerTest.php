<?php

namespace Tests\Feature;

use App\Models\AtencionMedica;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Cobertura del controlador de médicos: control de acceso vía Policy
 * (viewAny para todos los roles clínicos, create/update/delete solo
 * administrador), CRUD completo, y el manejo elegante del error al
 * eliminar un médico con citas/atenciones asociadas.
 */
class MedicoControllerTest extends TestCase
{
    use RefreshDatabase;

    private function datosBasicos(): array
    {
        return [
            'nombres' => 'Carlos',
            'apellidos' => 'Ramírez Soto',
            'dni' => '87654321',
            'cmp' => 'CMP99999',
            'especialidad_id' => Especialidad::factory()->create()->id,
            'estado' => 1,
        ];
    }

    #[Test]
    public function administrador_puede_ver_el_listado_de_medicos(): void
    {
        $admin = User::factory()->activo2FA()->create(['role' => 'administrador']);

        $this->actingAs($admin)
            ->get(route('medicos.index'))
            ->assertOk();
    }

    #[Test]
    public function recepcion_puede_ver_el_listado_de_medicos(): void
    {
        // La Policy permite viewAny a recepción, para poder agendar citas.
        $recepcion = User::factory()->create(['role' => 'recepcion']);

        $this->actingAs($recepcion)
            ->get(route('medicos.index'))
            ->assertOk();
    }

    #[Test]
    public function medico_no_puede_crear_otro_medico(): void
    {
        $medico = User::factory()->create(['role' => 'medico']);

        $this->actingAs($medico)
            ->post(route('medicos.store'), $this->datosBasicos())
            ->assertForbidden();

        $this->assertDatabaseCount('medicos', 0);
    }

    #[Test]
    public function administrador_puede_registrar_un_medico(): void
    {
        $admin = User::factory()->activo2FA()->create(['role' => 'administrador']);

        $response = $this->actingAs($admin)
            ->post(route('medicos.store'), $this->datosBasicos());

        $response->assertRedirect(route('medicos.index'));
        $this->assertDatabaseHas('medicos', [
            'dni' => '87654321',
            'cmp' => 'CMP99999',
        ]);
    }

    #[Test]
    public function registrar_medico_con_crear_usuario_tambien_crea_su_cuenta_de_acceso(): void
    {
        $admin = User::factory()->activo2FA()->create(['role' => 'administrador']);

        $datos = array_merge($this->datosBasicos(), [
            'crear_usuario' => 1,
            'email' => 'carlos.ramirez@hospital.test',
            'password' => 'password123',
        ]);

        $this->actingAs($admin)->post(route('medicos.store'), $datos);

        $this->assertDatabaseHas('users', [
            'email' => 'carlos.ramirez@hospital.test',
        ]);

        $medico = Medico::where('dni', '87654321')->first();
        $this->assertNotNull($medico->user_id);
    }

    #[Test]
    public function medico_no_puede_editar_a_otro_medico(): void
    {
        $medicoUser = User::factory()->create(['role' => 'medico']);
        $medico = Medico::factory()->create();

        $this->actingAs($medicoUser)
            ->put(route('medicos.update', $medico), $this->datosBasicos())
            ->assertForbidden();
    }

    #[Test]
    public function administrador_puede_actualizar_un_medico(): void
    {
        $admin = User::factory()->activo2FA()->create(['role' => 'administrador']);
        $medico = Medico::factory()->create(['nombres' => 'Nombre Viejo']);

        $datos = $this->datosBasicos();
        $datos['nombres'] = 'Nombre Actualizado';

        $response = $this->actingAs($admin)
            ->put(route('medicos.update', $medico), $datos);

        $response->assertRedirect(route('medicos.index'));
        $this->assertDatabaseHas('medicos', [
            'id' => $medico->id,
            'nombres' => 'Nombre Actualizado',
        ]);
    }

    #[Test]
    public function medico_no_puede_eliminar_a_otro_medico(): void
    {
        $medicoUser = User::factory()->create(['role' => 'medico']);
        $medico = Medico::factory()->create();

        $this->actingAs($medicoUser)
            ->delete(route('medicos.destroy', $medico))
            ->assertForbidden();

        $this->assertDatabaseHas('medicos', ['id' => $medico->id]);
    }

    #[Test]
    public function administrador_puede_eliminar_un_medico_sin_citas_ni_atenciones(): void
    {
        $admin = User::factory()->activo2FA()->create(['role' => 'administrador']);
        $medico = Medico::factory()->create();

        $response = $this->actingAs($admin)
            ->delete(route('medicos.destroy', $medico));

        $response->assertRedirect(route('medicos.index'));
        $this->assertDatabaseMissing('medicos', ['id' => $medico->id]);
    }

    #[Test]
    public function eliminar_un_medico_con_atenciones_registradas_muestra_error_en_vez_de_fallar(): void
    {
        // Protege la corrección ya hecha: antes esto tiraba un 500 crudo
        // de SQL Server por la restricción de FK (onDelete no action).
        // Ahora debe capturarse y mostrar un mensaje claro, sin eliminar
        // el registro.
        $admin = User::factory()->activo2FA()->create(['role' => 'administrador']);
        $medico = Medico::factory()->create();
        AtencionMedica::factory()->create(['medico_id' => $medico->id]);

        $response = $this->actingAs($admin)
            ->delete(route('medicos.destroy', $medico));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('medicos', ['id' => $medico->id]);
    }
}
