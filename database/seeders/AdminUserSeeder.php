<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * 👇 NUEVO: antes de este seeder, no existía ninguna forma automática
     * de crear el primer administrador del sistema — había que crearlo a
     * mano en Tinker en cada entorno nuevo. Con esto, cualquiera que clone
     * el repo y corra `php artisan migrate --seed` tiene un admin listo
     * desde el primer minuto.
     *
     * Usa updateOrCreate para que correr el seeder varias veces (por
     * ejemplo, tras un migrate:fresh --seed) no falle por email duplicado.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@hospital.test'],
            [
                'name' => 'Administrador',
                'password' => 'password123',
                'role' => 'administrador',
                'email_verified_at' => now(),
            ]
        );
    }
}
