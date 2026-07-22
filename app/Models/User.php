<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * 👇 CORREGIDO: 'role' ya no es una columna real (se eliminó de la BD),
     * ahora es 'role_id' respaldado por una FK a la tabla `roles`. Se deja
     * 'role' en fillable porque el mutator de abajo (setRoleAttribute)
     * intercepta las asignaciones a 'role' y las convierte en role_id.
     * Esto evita tener que tocar controladores/formularios existentes
     * que ya usan 'role' => 'medico', etc.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relación real con la tabla roles.
     */
    public function rol()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function medico()
    {
        return $this->hasOne(Medico::class);
    }

    /**
     * 👇 NUEVO: accessor que hace que $user->role siga devolviendo el
     * string del rol (ej. "medico"), como antes, aunque ahora la columna
     * real es role_id. Así todo tu código existente (Blade, controladores,
     * hasRole()) sigue funcionando sin cambios.
     */
    public function getRoleAttribute(): ?string
    {
        return $this->rol?->nombre;
    }

    /**
     * 👇 NUEVO: mutator que intercepta 'role' => 'medico' al crear/editar
     * un usuario, y lo traduce a role_id buscando el rol correspondiente
     * en la tabla `roles`. Si el rol no existe, lanza un error claro en
     * vez de guardar un dato inválido silenciosamente (el problema
     * original que teníamos con el string libre).
     */
    public function setRoleAttribute(string $value): void
    {
        $role = Role::where('nombre', $value)->first();

        if (! $role) {
            throw new \InvalidArgumentException(
                "El rol '{$value}' no existe. Roles válidos: " .
                Role::pluck('nombre')->implode(', ')
            );
        }

        $this->attributes['role_id'] = $role->id;
    }

    /**
     * Sin cambios: sigue funcionando igual, porque sigue leyendo
     * $this->role, que ahora viene del accessor de arriba.
     */
    public function hasRole(string|array $roles): bool
    {
        return in_array($this->role, (array) $roles);
    }
}