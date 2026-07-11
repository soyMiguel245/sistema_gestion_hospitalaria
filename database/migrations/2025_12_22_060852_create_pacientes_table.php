<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();

            // IDENTIFICACIÓN
            $table->string('dni', 15)->unique();
            $table->string('numero_historia_clinica')->unique();

            // DATOS PERSONALES
            $table->string('nombres');
            $table->string('apellidos');
            $table->date('fecha_nacimiento');
            $table->enum('sexo', ['Masculino', 'Femenino', 'Otro']);
            $table->string('estado_civil')->nullable();
            $table->string('nacionalidad')->nullable();

            // CONTACTO
            $table->string('telefono')->nullable();
            $table->string('correo')->nullable();
            $table->string('direccion')->nullable();

            // EMERGENCIA
            $table->string('contacto_emergencia')->nullable();
            $table->string('telefono_emergencia')->nullable();

            // DATOS CLÍNICOS
            $table->string('tipo_sangre', 5)->nullable();
            $table->text('alergias')->nullable();
            $table->text('enfermedades_cronicas')->nullable();
            $table->text('observaciones')->nullable();

            // ADMINISTRATIVO
            $table->enum('tipo_seguro', ['SIS', 'ESSALUD', 'Privado'])->nullable();
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
            $table->timestamp('fecha_registro');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
