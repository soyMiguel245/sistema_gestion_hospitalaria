<?php

namespace Tests\Unit;

use App\Models\AtencionMedica;
use App\Models\User;
use App\Policies\AtencionMedicaPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AtencionMedicaPolicyTest extends TestCase
{
    use RefreshDatabase;

    private AtencionMedicaPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new AtencionMedicaPolicy();
    }

    private function usuario(string $rol): User
    {
        return User::factory()->create(['role' => $rol]);
    }

    #[Test]
    public function viewAny_permite_administrador_medico_y_enfermera_pero_no_recepcion(): void
    {
        $this->assertTrue($this->policy->viewAny($this->usuario('administrador')));
        $this->assertTrue($this->policy->viewAny($this->usuario('medico')));
        $this->assertTrue($this->policy->viewAny($this->usuario('enfermera')));
        $this->assertFalse($this->policy->viewAny($this->usuario('recepcion')));
    }

    #[Test]
    public function view_sigue_la_misma_regla_que_viewAny(): void
    {
        $atencion = AtencionMedica::factory()->create();

        $this->assertTrue($this->policy->view($this->usuario('enfermera'), $atencion));
        $this->assertFalse($this->policy->view($this->usuario('recepcion'), $atencion));
    }

    #[Test]
    public function create_solo_permite_administrador_y_medico(): void
    {
        $this->assertTrue($this->policy->create($this->usuario('administrador')));
        $this->assertTrue($this->policy->create($this->usuario('medico')));

        $this->assertFalse($this->policy->create($this->usuario('enfermera')));
        $this->assertFalse($this->policy->create($this->usuario('recepcion')));
    }

    #[Test]
    public function update_solo_permite_administrador_y_medico(): void
    {
        $atencion = AtencionMedica::factory()->create();

        $this->assertTrue($this->policy->update($this->usuario('administrador'), $atencion));
        $this->assertTrue($this->policy->update($this->usuario('medico'), $atencion));
        $this->assertFalse($this->policy->update($this->usuario('enfermera'), $atencion));
    }

    #[Test]
    public function delete_solo_permite_administrador_ni_siquiera_el_medico_que_la_registro(): void
    {
        $atencion = AtencionMedica::factory()->create();

        $this->assertTrue($this->policy->delete($this->usuario('administrador'), $atencion));
        $this->assertFalse($this->policy->delete($this->usuario('medico'), $atencion));
        $this->assertFalse($this->policy->delete($this->usuario('enfermera'), $atencion));
        $this->assertFalse($this->policy->delete($this->usuario('recepcion'), $atencion));
    }
}
