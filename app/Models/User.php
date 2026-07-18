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
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // <-- FIX: faltaba, por eso el rol no se guardaba al crear/editar usuarios
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Helper para verificar el rol del usuario. Lo usaremos en las Policies
     * en vez de comparar $user->role === 'algo' repetido en cada una.
     *
     * Uso: $user->hasRole('administrador')
     *      $user->hasRole(['medico', 'administrador'])
     */
    public function hasRole(string|array $roles): bool
    {
        return in_array($this->role, (array) $roles);
    }
}