<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Encriptado en el modelo (ver User::twoFactorSecret cast) —
            // nunca se guarda el secreto TOTP en texto plano.
            $table->text('two_factor_secret')->nullable()->after('password');

            // Códigos de recuperación de emergencia, también encriptados.
            $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_secret');

            // Null = 2FA no confirmado/activo. Con fecha = activo desde entonces.
            $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_recovery_codes');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at']);
        });
    }
};
