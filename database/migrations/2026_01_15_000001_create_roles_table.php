<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['nombre' => 'administrador', 'descripcion' => 'Acceso total al sistema', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'medico', 'descripcion' => 'Atiende pacientes, registra diagnósticos y tratamientos', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'recepcion', 'descripcion' => 'Gestiona pacientes y citas, sin acceso a contenido clínico', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'enfermera', 'descripcion' => 'Registra signos vitales y apoya la atención médica', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
