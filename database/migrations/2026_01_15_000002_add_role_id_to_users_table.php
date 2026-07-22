<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// Simplificada a propósito: como este proyecto está en desarrollo y no
// tiene usuarios reales todavía, no hace falta migrar datos desde una
// columna `role` (string) vieja — directamente creamos `role_id` como
// debió haber sido desde el inicio.
return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')
                  ->nullable()
                  ->after('password')
                  ->constrained('roles');
        });

        // Si ya tienes usuarios de prueba en la BD al momento de correr esto,
        // se les asigna 'recepcion' por defecto (mismo comportamiento que
        // tenía el default del string viejo), para no dejar filas con
        // role_id en null.
        $recepcionId = DB::table('roles')->where('nombre', 'recepcion')->value('id');
        DB::table('users')->whereNull('role_id')->update(['role_id' => $recepcionId]);

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
        });
    }
};