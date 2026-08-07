<?php

namespace Tests\Unit;

use App\Http\Controllers\ReporteController;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReporteControllerRangoFechasTest extends TestCase
{
    private function llamarRangoFechas(Request $request): array
    {
        $controller = new ReporteController;

        $reflection = new \ReflectionMethod($controller, 'rangoFechas');
        $reflection->setAccessible(true);

        return $reflection->invoke($controller, $request);
    }

    #[Test]
    public function sin_fechas_en_la_peticion_usa_el_ultimo_mes_por_defecto(): void
    {
        // 👇 CORREGIDO: el controlador aplica startOfDay()/endOfDay(),
        // así que $inicio no es "ahora menos un mes" sino la MEDIANOCHE
        // de ese día. La ventana de comparación debe reflejar eso.
        $request = new Request;

        [$inicio, $fin] = $this->llamarRangoFechas($request);

        $this->assertEquals(
            now()->subMonth()->startOfDay()->format('Y-m-d H:i:s'),
            $inicio->format('Y-m-d H:i:s')
        );

        $this->assertEquals(
            now()->endOfDay()->format('Y-m-d H:i:s'),
            $fin->format('Y-m-d H:i:s')
        );
    }

    #[Test]
    public function con_fecha_inicio_y_fin_explicitas_las_respeta_en_vez_del_fallback(): void
    {
        $request = new Request([
            'fecha_inicio' => '2026-01-01',
            'fecha_fin' => '2026-01-31',
        ]);

        [$inicio, $fin] = $this->llamarRangoFechas($request);

        $this->assertEquals('2026-01-01 00:00:00', $inicio->format('Y-m-d H:i:s'));
        $this->assertEquals('2026-01-31 23:59:59', $fin->format('Y-m-d H:i:s'));
    }

    #[Test]
    public function solo_fecha_inicio_definida_usa_fallback_solo_para_fin(): void
    {
        // 👇 CORREGIDO: mismo ajuste — $fin usa endOfDay(), no "ahora".
        $request = new Request(['fecha_inicio' => '2026-01-01']);

        [$inicio, $fin] = $this->llamarRangoFechas($request);

        $this->assertEquals('2026-01-01 00:00:00', $inicio->format('Y-m-d H:i:s'));
        $this->assertEquals(
            now()->endOfDay()->format('Y-m-d H:i:s'),
            $fin->format('Y-m-d H:i:s')
        );
    }
}
