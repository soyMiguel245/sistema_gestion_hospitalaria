<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Cobertura del middleware EnsureAdminHas2FA: el rol Administrador debe
 * tener 2FA activo antes de poder usar cualquier módulo del sistema,
 * excepto las rutas exentas (perfil, activación de 2FA, logout).
 */
class EnsureAdminHas2FATest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function administrador_sin_2fa_es_redirigido_a_su_perfil_al_intentar_usar_el_sistema(): void
    {
        $admin = User::factory()->create(['role' => 'administrador']);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertRedirect(route('profile.edit'));
    }

    #[Test]
    public function administrador_sin_2fa_puede_acceder_a_su_propio_perfil(): void
    {
        $admin = User::factory()->create(['role' => 'administrador']);

        $this->actingAs($admin)
            ->get(route('profile.edit'))
            ->assertOk();
    }

    #[Test]
    public function administrador_sin_2fa_puede_activar_2fa(): void
    {
        // La ruta de activación debe quedar exenta, o el admin nunca
        // podría cumplir el requisito que el propio middleware le exige.
        $admin = User::factory()->create(['role' => 'administrador']);

        $this->actingAs($admin)
            ->post(route('two-factor.enable'))
            ->assertRedirect(route('profile.edit'));
    }

    #[Test]
    public function administrador_sin_2fa_puede_cerrar_sesion(): void
    {
        $admin = User::factory()->create(['role' => 'administrador']);

        $this->actingAs($admin)
            ->post(route('logout'))
            ->assertRedirect('/');
    }

    #[Test]
    public function administrador_con_2fa_activo_puede_usar_el_sistema_normalmente(): void
    {
        $admin = User::factory()->create(['role' => 'administrador']);
        $admin->forceFill(['two_factor_confirmed_at' => now()])->save();

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk();
    }

    #[Test]
    public function medico_sin_2fa_puede_usar_el_sistema_normalmente(): void
    {
        // El middleware solo aplica al rol administrador.
        $medico = User::factory()->create(['role' => 'medico']);

        $this->actingAs($medico)
            ->get('/dashboard')
            ->assertOk();
    }
}