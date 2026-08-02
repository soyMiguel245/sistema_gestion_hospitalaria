<?php

namespace Tests\Feature;

use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Cobertura del controlador de especialidades: control de acceso vía
 * Policy (viewAny para todos los roles clínicos, create/update/delete
 * solo administrador), CRUD completo, y el chequeo explícito que impide
 * eliminar una especialidad con médicos asociados (antes la BD tenía
 * cascadeOnDelete, lo que habría borrado médicos en cascada).
 */
class EspecialidadControllerTest extends TestCase
{
    use RefreshDatabase;

    private function datosBasicos(): array
    {
        return [
            'nombre' => 'Endocrinología',
            'descripcion' => 'Atención de trastornos hormonales',
            'estado' => 1,
        ];
    }

    #[Test]
    public function administrador_puede_ver_el_listado_de_especialidades(): void
    {
        $admin = User::factory()->create(['role' => 'administrador']);

        $this->actingAs($admin)
            ->get(route('especialidades.index'))
            ->assertOk();
    }

    #[Test]
    public function enfermera_puede_ver_el_listado_de_especialidades(): void
    {
        $enfermera = User::factory()->create(['role' => 'enfermera']);

        $this->actingAs($enfermera)
            ->get(route('especialidades.index'))
            ->assertOk();
    }

    #[Test]
    public function medico_no_puede_crear_una_especialidad(): void
    {
        $medico = User::factory()->create(['role' => 'medico']);

        $this->actingAs($medico)
            ->post(route('especialidades.store'), $this->datosBasicos())
            ->assertForbidden();

        $this->assertDatabaseCount('especialidades', 0);
    }

    #[Test]
    public function administrador_puede_registrar_una_especialidad(): void
    {
        $admin = User::factory()->create(['role' => 'administrador']);

        $response = $this->actingAs($admin)
            ->post(route('especialidades.store'), $this->datosBasicos());

        $response->assertRedirect(route('especialidades.index'));
        $this->assertDatabaseHas('especialidades', ['nombre' => 'Endocrinología']);
    }

    #[Test]
    public function no_se_puede_registrar_una_especialidad_con_nombre_repetido(): void
    {
        $admin = User::factory()->create(['role' => 'administrador']);
        Especialidad::factory()->create(['nombre' => 'Endocrinología']);

        $this->actingAs($admin)
            ->post(route('especialidades.store'), $this->datosBasicos())
            ->assertSessionHasErrors('nombre');

        $this->assertDatabaseCount('especialidades', 1);
    }

    #[Test]
    public function medico_no_puede_editar_una_especialidad(): void
    {
        $medicoUser = User::factory()->create(['role' => 'medico']);
        $especialidad = Especialidad::factory()->create();

        $this->actingAs($medicoUser)
            ->put(route('especialidades.update', $especialidad), $this->datosBasicos())
            ->assertForbidden();
    }

    #[Test]
    public function administrador_puede_actualizar_una_especialidad(): void
    {
        $admin = User::factory()->create(['role' => 'administrador']);
        $especialidad = Especialidad::factory()->create(['nombre' => 'Nombre Viejo']);

        $datos = $this->datosBasicos();
        $datos['nombre'] = 'Nombre Actualizado';

        $response = $this->actingAs($admin)
            ->put(route('especialidades.update', $especialidad), $datos);

        $response->assertRedirect(route('especialidades.index'));
        $this->assertDatabaseHas('especialidades', [
            'id' => $especialidad->id,
            'nombre' => 'Nombre Actualizado',
        ]);
    }

    #[Test]
    public function medico_no_puede_eliminar_una_especialidad(): void
    {
        $medicoUser = User::factory()->create(['role' => 'medico']);
        $especialidad = Especialidad::factory()->create();

        $this->actingAs($medicoUser)
            ->delete(route('especialidades.destroy', $especialidad))
            ->assertForbidden();

        $this->assertDatabaseHas('especialidades', ['id' => $especialidad->id]);
    }

    #[Test]
    public function administrador_puede_eliminar_una_especialidad_sin_medicos_asociados(): void
    {
        $admin = User::factory()->create(['role' => 'administrador']);
        $especialidad = Especialidad::factory()->create();

        $response = $this->actingAs($admin)
            ->delete(route('especialidades.destroy', $especialidad));

        $response->assertRedirect(route('especialidades.index'));
        $this->assertDatabaseMissing('especialidades', ['id' => $especialidad->id]);
    }

    #[Test]
    public function no_se_puede_eliminar_una_especialidad_con_medicos_asociados(): void
    {
        // Protege el chequeo explícito: antes la BD tenía cascadeOnDelete,
        // lo que habría borrado médicos en cascada al eliminar la
        // especialidad. Ahora debe bloquearse con un mensaje claro,
        // sin tocar ni la especialidad ni el médico.
        $admin = User::factory()->create(['role' => 'administrador']);
        $especialidad = Especialidad::factory()->create();
        Medico::factory()->create(['especialidad_id' => $especialidad->id]);

        $response = $this->actingAs($admin)
            ->delete(route('especialidades.destroy', $especialidad));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('especialidades', ['id' => $especialidad->id]);
        $this->assertDatabaseCount('medicos', 1);
    }
}
