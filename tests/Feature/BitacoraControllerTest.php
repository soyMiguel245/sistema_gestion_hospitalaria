<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Cobertura del controlador de bitácora de auditoría: solo administrador
 * puede acceder, y el filtro por módulo (log_name) debe funcionar bien,
 * porque de esto depende que la auditoría del sistema sea confiable.
 */
class BitacoraControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function administrador_puede_ver_la_bitacora(): void
    {
        $admin = User::factory()->create(['role' => 'administrador']);

        $this->actingAs($admin)
            ->get('/bitacora')
            ->assertOk();
    }

    #[Test]
    public function medico_no_puede_ver_la_bitacora(): void
    {
        $medico = User::factory()->create(['role' => 'medico']);

        $this->actingAs($medico)
            ->get('/bitacora')
            ->assertForbidden();
    }

    #[Test]
    public function recepcion_no_puede_ver_la_bitacora(): void
    {
        $recepcion = User::factory()->create(['role' => 'recepcion']);

        $this->actingAs($recepcion)
            ->get('/bitacora')
            ->assertForbidden();
    }

    #[Test]
    public function un_usuario_no_autenticado_no_puede_ver_la_bitacora(): void
    {
        // El controlador no usa el middleware auth: si no hay usuario,
        // el closure hace abort(403) directo, no redirige a login.
        $this->get('/bitacora')
            ->assertForbidden();
    }

    #[Test]
    public function el_filtro_por_modulo_solo_muestra_registros_de_ese_log_name(): void
    {
        $admin = User::factory()->create(['role' => 'administrador']);

        Activity::create([
            'log_name' => 'citas',
            'description' => 'Registro de prueba en módulo citas',
            'properties' => collect([]),
        ]);

        Activity::create([
            'log_name' => 'atenciones',
            'description' => 'Registro de prueba en módulo atenciones',
            'properties' => collect([]),
        ]);

        $response = $this->actingAs($admin)->get('/bitacora?modulo=citas');

        $response->assertOk();
        $registros = $response->viewData('registros');

        $this->assertCount(1, $registros);
        $this->assertEquals('citas', $registros->first()->log_name);
    }

    #[Test]
    public function sin_filtro_de_modulo_se_muestran_todos_los_registros(): void
    {
        $admin = User::factory()->create(['role' => 'administrador']);

        Activity::create([
            'log_name' => 'citas',
            'description' => 'Registro de prueba en módulo citas',
            'properties' => collect([]),
        ]);

        Activity::create([
            'log_name' => 'atenciones',
            'description' => 'Registro de prueba en módulo atenciones',
            'properties' => collect([]),
        ]);

        $response = $this->actingAs($admin)->get('/bitacora');

        $response->assertOk();
        $registros = $response->viewData('registros');

        $this->assertCount(2, $registros);
    }
}