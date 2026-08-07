<?php

namespace Tests\Unit;

use App\Models\AtencionMedica;
use App\Models\Medico;
use App\Models\Paciente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HistorialClinicoModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function historial_clinico_devuelve_solo_las_atenciones_del_paciente_correcto(): void
    {
        $paciente1 = Paciente::factory()->create();
        $paciente2 = Paciente::factory()->create();
        $medico = Medico::factory()->create();

        AtencionMedica::factory()->create(['paciente_id' => $paciente1->id, 'medico_id' => $medico->id]);
        AtencionMedica::factory()->create(['paciente_id' => $paciente2->id, 'medico_id' => $medico->id]);

        $historial = $paciente1->historialClinico()->get();

        $this->assertCount(1, $historial);
        $this->assertEquals($paciente1->id, $historial->first()->paciente_id);
    }

    #[Test]
    public function historial_clinico_ordena_las_atenciones_de_mas_reciente_a_mas_antigua(): void
    {
        $paciente = Paciente::factory()->create();
        $medico = Medico::factory()->create();

        $antigua = AtencionMedica::factory()->create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'created_at' => now()->subDays(5),
        ]);

        $reciente = AtencionMedica::factory()->create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'created_at' => now(),
        ]);

        $historial = $paciente->historialClinico()->get();

        $this->assertEquals($reciente->id, $historial->first()->id);
        $this->assertEquals($antigua->id, $historial->last()->id);
    }

    #[Test]
    public function historial_clinico_viene_vacio_si_el_paciente_no_tiene_atenciones(): void
    {
        $paciente = Paciente::factory()->create();

        $historial = $paciente->historialClinico()->get();

        $this->assertCount(0, $historial);
    }

    #[Test]
    public function historial_clinico_carga_las_relaciones_diagnosticos_medico_y_cita_de_una_vez(): void
    {
        $paciente = Paciente::factory()->create();
        $medico = Medico::factory()->create();
        AtencionMedica::factory()->create(['paciente_id' => $paciente->id, 'medico_id' => $medico->id]);

        $historial = $paciente->historialClinico()->get();
        $primera = $historial->first();

        $this->assertTrue($primera->relationLoaded('diagnosticos'));
        $this->assertTrue($primera->relationLoaded('medico'));
        $this->assertTrue($primera->relationLoaded('cita'));
    }
}
