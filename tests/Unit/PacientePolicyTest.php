<?php

namespace Tests\Unit;

use App\Models\Paciente;
use App\Models\User;
use App\Policies\PacientePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PacientePolicyTest extends TestCase
{
    use RefreshDatabase;

    private PacientePolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new PacientePolicy();
    }

    private function usuario(string $rol): User
    {
        return User::factory()->create(['role' => $rol]);
    }

    #[Test]
    public function viewAny_permite_a_los_cuatro_roles_clinicos(): void
    {
        foreach (['administrador', 'medico', 'recepcion', 'enfermera'] as $rol) {
            $this->assertTrue(
                $this->policy->viewAny($this->usuario($rol)),
                "El rol '{$rol}' debería poder ver el listado de pacientes."
            );
        }
    }

    #[Test]
    public function create_solo_permite_administrador_y_recepcion(): void
    {
        $this->assertTrue($this->policy->create($this->usuario('administrador')));
        $this->assertTrue($this->policy->create($this->usuario('recepcion')));

        $this->assertFalse($this->policy->create($this->usuario('medico')));
        $this->assertFalse($this->policy->create($this->usuario('enfermera')));
    }

    #[Test]
    public function update_solo_permite_administrador_y_recepcion(): void
    {
        $paciente = Paciente::factory()->create();

        $this->assertTrue($this->policy->update($this->usuario('administrador'), $paciente));
        $this->assertTrue($this->policy->update($this->usuario('recepcion'), $paciente));
        $this->assertFalse($this->policy->update($this->usuario('enfermera'), $paciente));
    }

    #[Test]
    public function delete_solo_permite_administrador(): void
    {
        $paciente = Paciente::factory()->create();

        $this->assertTrue($this->policy->delete($this->usuario('administrador'), $paciente));
        $this->assertFalse($this->policy->delete($this->usuario('recepcion'), $paciente));
        $this->assertFalse($this->policy->delete($this->usuario('medico'), $paciente));
        $this->assertFalse($this->policy->delete($this->usuario('enfermera'), $paciente));
    }
}
