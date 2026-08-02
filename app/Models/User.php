<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            // 👇 NUEVO: encriptados automáticamente en BD, desencriptados
            // al acceder en PHP. Nunca quedan en texto plano ni en un
            // backup de la base de datos.
            'two_factor_secret' => 'encrypted',
            'two_factor_recovery_codes' => 'encrypted:array',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function rol()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function medico()
    {
        return $this->hasOne(Medico::class);
    }

    public function getRoleAttribute(): ?string
    {
        return $this->rol?->nombre;
    }

    public function setRoleAttribute(string $value): void
    {
        $role = Role::where('nombre', $value)->first();

        if (! $role) {
            throw new \InvalidArgumentException(
                "El rol '{$value}' no existe. Roles válidos: ".
                Role::pluck('nombre')->implode(', ')
            );
        }

        $this->attributes['role_id'] = $role->id;
    }

    public function hasRole(string|array $roles): bool
    {
        return in_array($this->role, (array) $roles);
    }

    /* ============================================================
     * 👇 NUEVO — Autenticación de dos factores (2FA)
     * ============================================================ */

    /**
     * ¿Este usuario tiene 2FA activo y confirmado?
     */
    public function tieneDosFactoresActivo(): bool
    {
        return ! is_null($this->two_factor_confirmed_at);
    }

    /**
     * Genera un secreto TOTP nuevo (aún sin confirmar) y lo guarda.
     * Se llama al iniciar el proceso de activación.
     */
    public function generarSecretoDosFactores(): string
    {
        $secreto = app(Google2FA::class)->generateSecretKey();

        $this->forceFill([
            'two_factor_secret' => $secreto,
            'two_factor_confirmed_at' => null,
        ])->save();

        return $secreto;
    }

    /**
     * URL otpauth:// que se codifica en el QR, para que apps como
     * Google Authenticator / Authy la puedan escanear.
     */
    public function urlQrDosFactores(): string
    {
        return app(Google2FA::class)->getQRCodeUrl(
            config('app.name'),
            $this->email,
            $this->two_factor_secret
        );
    }

    /**
     * Verifica un código de 6 dígitos contra el secreto guardado.
     */
    public function verificarCodigoDosFactores(string $codigo): bool
    {
        if (! $this->two_factor_secret) {
            return false;
        }

        return app(Google2FA::class)->verifyKey($this->two_factor_secret, $codigo);
    }

    /**
     * Marca el 2FA como confirmado y genera 8 códigos de recuperación
     * de un solo uso (por si el usuario pierde su teléfono).
     */
    public function confirmarDosFactores(): array
    {
        $codigos = collect(range(1, 8))
            ->map(fn () => Str::random(10).'-'.Str::random(10))
            ->all();

        $this->forceFill([
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => $codigos,
        ])->save();

        return $codigos;
    }

    /**
     * Desactiva el 2FA por completo (el usuario decide quitarlo).
     */
    public function desactivarDosFactores(): void
    {
        $this->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();
    }

    /**
     * Consume un código de recuperación si es válido (uso único).
     */
    public function usarCodigoRecuperacion(string $codigo): bool
    {
        $codigos = $this->two_factor_recovery_codes ?? [];

        if (! in_array($codigo, $codigos, true)) {
            return false;
        }

        $this->forceFill([
            'two_factor_recovery_codes' => array_values(array_diff($codigos, [$codigo])),
        ])->save();

        return true;
    }
}
