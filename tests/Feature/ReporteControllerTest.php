<?php

namespace Tests\Feature;

use App\Models\AtencionMedica;
use App\Models\Diagnostico;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Cobertura del controlador de reportes: control de acceso (solo
 * administrador/médico), exportación a PDF con datos reales (protege
 * el bug ya corregido de PDFs vacíos), export CSV, y tipo inválido.
 */
class ReporteControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function recepcion_no_puede_ver_el_dashboard_de_reportes(): void
    {
        $recepcion = User::factory()->create(['role' => 'recepcion']);

        $this->actingAs($recepcion)
            ->get('/reportes/dashboard')
            ->assertForbidden();
    }

    #[Test]
    public function administrador_puede_ver_el_dashboard_de_reportes(): void
    {
        $admin = User::factory()->create(['role' => 'administrador']);

        $this->actingAs($admin)
            ->get('/reportes/dashboard')
            ->assertOk();
    }

    #[Test]
    public function medico_puede_ver_el_dashboard_de_reportes(): void
    {
        $medico = User::factory()->create(['role' => 'medico']);

        $this->actingAs($medico)
            ->get('/reportes/dashboard')
            ->assertOk();
    }

    #[Test]
    public function recepcion_no_puede_exportar_el_pdf(): void
    {
        $recepcion = User::factory()->create(['role' => 'recepcion']);

        $this->actingAs($recepcion)
            ->get('/reportes/exportar-pdf')
            ->assertForbidden();
    }

    #[Test]
    public function exportar_pdf_envia_los_datos_reales_del_periodo_a_la_vista(): void
    {
        // Este es el test que protege el bug ya corregido: antes el PDF
        // se descargaba vacío porque no se le pasaban datos a la vista.
        // Aquí confirmamos que pacientesAtendidos llega con el conteo
        // real (3), no vacío ni en cero por defecto.
        $admin = User::factory()->create(['role' => 'administrador']);

        AtencionMedica::factory()->count(3)->create();

        $mockPdf = Mockery::mock(\Barryvdh\DomPDF\PDF::class);
        $mockPdf->shouldReceive('download')->once()->andReturn(response('contenido-pdf-falso', 200));

        Pdf::shouldReceive('loadView')
            ->once()
            ->with('reportes.export_pdf', Mockery::on(function ($datos) {
                return $datos['pacientesAtendidos'] === 3
                    && array_key_exists('diagnosticos', $datos)
                    && array_key_exists('ingresos', $datos)
                    && array_key_exists('fecha_inicio', $datos)
                    && array_key_exists('fecha_fin', $datos);
            }))
            ->andReturn($mockPdf);

        $this->actingAs($admin)
            ->get('/reportes/exportar-pdf')
            ->assertOk();
    }

    #[Test]
    public function exportar_pdf_incluye_los_diagnosticos_del_periodo(): void
    {
        $admin = User::factory()->create(['role' => 'administrador']);

        $atencion = AtencionMedica::factory()->create();
        Diagnostico::factory()->create([
            'atencion_medica_id' => $atencion->id,
            'descripcion' => 'Hipertensión arterial',
            'cie10' => 'I10',
        ]);

        $mockPdf = Mockery::mock(\Barryvdh\DomPDF\PDF::class);
        $mockPdf->shouldReceive('download')->once()->andReturn(response('contenido-pdf-falso', 200));

        Pdf::shouldReceive('loadView')
            ->once()
            ->with('reportes.export_pdf', Mockery::on(function ($datos) {
                return $datos['diagnosticos']->count() === 1
                    && $datos['diagnosticos']->first()->descripcion === 'Hipertensión arterial';
            }))
            ->andReturn($mockPdf);

        $this->actingAs($admin)->get('/reportes/exportar-pdf');
    }

    #[Test]
    public function tipo_de_reporte_invalido_en_export_devuelve_404(): void
    {
        $admin = User::factory()->create(['role' => 'administrador']);

        $this->actingAs($admin)
            ->get('/reportes/export/algo_que_no_existe')
            ->assertNotFound();
    }

    #[Test]
    public function medico_puede_descargar_el_csv_de_pacientes(): void
    {
        $medico = User::factory()->create(['role' => 'medico']);

        $response = $this->actingAs($medico)->get('/reportes/export/pacientes');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=utf-8');
    }

    #[Test]
    public function recepcion_no_puede_descargar_ningun_csv_de_reportes(): void
    {
        $recepcion = User::factory()->create(['role' => 'recepcion']);

        $this->actingAs($recepcion)
            ->get('/reportes/export/pacientes')
            ->assertForbidden();
    }
}