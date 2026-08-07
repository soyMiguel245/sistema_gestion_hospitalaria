<?php

namespace Tests\Unit;

use App\Models\Cita;
use App\Models\Medico;
use App\Models\Paciente;
use App\Rules\MedicoDisponible;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MedicoDisponibleTest extends TestCase
{
    use RefreshDatabase;

    private function citaExistente(int $medicoId, string $horaInicio, int $duracion = 30, string $estado = 'Programada'): Cita
    {
        return Cita::factory()->create([
            'medico_id' => $medicoId,
            'paciente_id' => Paciente::factory()->create()->id,
            'fecha_hora' => $horaInicio,
            'duracion' => $duracion,
            'estado' => $estado,
        ]);
    }

    #[Test]
    public function no_falla_si_no_hay_medico_o_fecha_definidos(): void
    {
        $regla = new MedicoDisponible(null, null);

        $fallo = null;
        $regla->validate('fecha_hora', null, function ($mensaje) use (&$fallo) {
            $fallo = $mensaje;
        });

        $this->assertNull($fallo);
    }

    #[Test]
    public function rechaza_cuando_hay_choque_exacto_de_horario(): void
    {
        $medico = Medico::factory()->create();
        $this->citaExistente($medico->id, '2026-09-10 10:00:00', 30);

        $regla = new MedicoDisponible($medico->id, '2026-09-10 10:00:00', 30);

        $fallo = null;
        $regla->validate('fecha_hora', null, function ($mensaje) use (&$fallo) {
            $fallo = $mensaje;
        });

        $this->assertNotNull($fallo);
    }

    #[Test]
    public function rechaza_cuando_hay_solapamiento_parcial_no_solo_choque_exacto(): void
    {
        $medico = Medico::factory()->create();
        $this->citaExistente($medico->id, '2026-09-10 10:00:00', 30);

        $regla = new MedicoDisponible($medico->id, '2026-09-10 10:15:00', 30);

        $fallo = null;
        $regla->validate('fecha_hora', null, function ($mensaje) use (&$fallo) {
            $fallo = $mensaje;
        });

        $this->assertNotNull($fallo);
    }

    #[Test]
    public function permite_agendar_justo_cuando_termina_la_cita_anterior(): void
    {
        $medico = Medico::factory()->create();
        $this->citaExistente($medico->id, '2026-09-10 10:00:00', 30);

        $regla = new MedicoDisponible($medico->id, '2026-09-10 10:30:00', 30);

        $fallo = null;
        $regla->validate('fecha_hora', null, function ($mensaje) use (&$fallo) {
            $fallo = $mensaje;
        });

        $this->assertNull($fallo, 'No debería haber conflicto cuando la nueva cita empieza justo cuando termina la anterior.');
    }

    #[Test]
    public function permite_horario_completamente_libre(): void
    {
        $medico = Medico::factory()->create();
        $this->citaExistente($medico->id, '2026-09-10 10:00:00', 30);

        $regla = new MedicoDisponible($medico->id, '2026-09-10 14:00:00', 30);

        $fallo = null;
        $regla->validate('fecha_hora', null, function ($mensaje) use (&$fallo) {
            $fallo = $mensaje;
        });

        $this->assertNull($fallo);
    }

    #[Test]
    public function ignora_citas_canceladas_al_verificar_choque(): void
    {
        $medico = Medico::factory()->create();
        $this->citaExistente($medico->id, '2026-09-10 10:00:00', 30, 'Cancelada');

        $regla = new MedicoDisponible($medico->id, '2026-09-10 10:00:00', 30);

        $fallo = null;
        $regla->validate('fecha_hora', null, function ($mensaje) use (&$fallo) {
            $fallo = $mensaje;
        });

        $this->assertNull($fallo, 'Una cita Cancelada no debería bloquear el horario.');
    }

    #[Test]
    public function ignora_citas_con_estado_no_asistio(): void
    {
        $medico = Medico::factory()->create();
        $this->citaExistente($medico->id, '2026-09-10 10:00:00', 30, 'No asistió');

        $regla = new MedicoDisponible($medico->id, '2026-09-10 10:00:00', 30);

        $fallo = null;
        $regla->validate('fecha_hora', null, function ($mensaje) use (&$fallo) {
            $fallo = $mensaje;
        });

        $this->assertNull($fallo);
    }

    #[Test]
    public function ignora_la_propia_cita_al_editar(): void
    {
        $medico = Medico::factory()->create();
        $cita = $this->citaExistente($medico->id, '2026-09-10 10:00:00', 30);

        $regla = new MedicoDisponible($medico->id, '2026-09-10 10:00:00', 30, $cita->id);

        $fallo = null;
        $regla->validate('fecha_hora', null, function ($mensaje) use (&$fallo) {
            $fallo = $mensaje;
        });

        $this->assertNull($fallo, 'Al editar, la cita no debería chocar consigo misma.');
    }

    #[Test]
    public function distinto_medico_mismo_horario_no_genera_conflicto(): void
    {
        $medico1 = Medico::factory()->create();
        $medico2 = Medico::factory()->create();
        $this->citaExistente($medico1->id, '2026-09-10 10:00:00', 30);

        $regla = new MedicoDisponible($medico2->id, '2026-09-10 10:00:00', 30);

        $fallo = null;
        $regla->validate('fecha_hora', null, function ($mensaje) use (&$fallo) {
            $fallo = $mensaje;
        });

        $this->assertNull($fallo);
    }
}
