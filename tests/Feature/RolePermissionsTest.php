<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Prueba automatizada de la matriz de roles que se verificó manualmente
 * durante la auditoría: administrador, medico, recepcion y enfermera
 * contra las rutas principales del sistema.
 *
 * Si alguien cambia una Policy sin querer y rompe un permiso, este test
 * lo detecta inmediatamente al correr `php artisan test`, sin tener que
 * volver a probar los 4 roles a mano uno por uno.
 */
class RolePermissionsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Un usuario de prueba por cada rol. Se crean una sola vez por test
     * gracias a RefreshDatabase (la BD se resetea entre tests, así que
     * cada test parte de cero).
     */
    private function usuarios(): array
    {
        return [
            'administrador' => User::factory()->create(['role' => 'administrador']),
            'medico'        => User::factory()->create(['role' => 'medico']),
            'recepcion'     => User::factory()->create(['role' => 'recepcion']),
            'enfermera'     => User::factory()->create(['role' => 'enfermera']),
        ];
    }

    /**
     * Matriz completa: rol => [ruta => status esperado].
     * 200 = debe poder entrar. 403 = debe ser rechazado.
     * Esta es exactamente la tabla que se probó a mano en el navegador.
     */
    private function matrizEsperada(): array
    {
        return [
            'administrador' => [
                'pacientes.index' => 200, 'pacientes.create' => 200,
                'citas.index' => 200, 'citas.create' => 200,
                'historias.index' => 200,
                'atenciones.index' => 200, 'atenciones.create' => 200,
                'medicos.index' => 200, 'medicos.create' => 200,
                'especialidades.index' => 200, 'especialidades.create' => 200,
                'reportes.index' => 200,
            ],
            'medico' => [
                'pacientes.index' => 200, 'pacientes.create' => 403,
                'citas.index' => 200, 'citas.create' => 403,
                'historias.index' => 200,
                'atenciones.index' => 200, 'atenciones.create' => 200,
                'medicos.index' => 200, 'medicos.create' => 403,
                'especialidades.index' => 200, 'especialidades.create' => 403,
                'reportes.index' => 200,
            ],
            'recepcion' => [
                'pacientes.index' => 200, 'pacientes.create' => 200,
                'citas.index' => 200, 'citas.create' => 200,
                'historias.index' => 403,
                'atenciones.index' => 403, 'atenciones.create' => 403,
                'medicos.index' => 200, 'medicos.create' => 403,
                'especialidades.index' => 200, 'especialidades.create' => 403,
                'reportes.index' => 403,
            ],
            'enfermera' => [
                'pacientes.index' => 200, 'pacientes.create' => 403,
                'citas.index' => 200, 'citas.create' => 403,
                'historias.index' => 200,
                'atenciones.index' => 200, 'atenciones.create' => 403,
                'medicos.index' => 200, 'medicos.create' => 403,
                'especialidades.index' => 200, 'especialidades.create' => 403,
                'reportes.index' => 403,
            ],
        ];
    }

    #[Test]
    public function la_matriz_de_roles_se_cumple_para_todas_las_rutas_principales(): void
    {
        $usuarios = $this->usuarios();
        $matriz = $this->matrizEsperada();

        foreach ($matriz as $rol => $rutas) {
            $user = $usuarios[$rol];

            foreach ($rutas as $nombreRuta => $statusEsperado) {
                $response = $this->actingAs($user)->get(route($nombreRuta));

                $response->assertStatus(
                    $statusEsperado,
                    "Fallo: rol '{$rol}' en ruta '{$nombreRuta}' — se esperaba {$statusEsperado}, se obtuvo {$response->status()}."
                );
            }
        }
    }

    #[Test]
    public function un_usuario_sin_rol_valido_no_deberia_poder_asignarse(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        User::factory()->create(['role' => 'rol_que_no_existe']);
    }
}