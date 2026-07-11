<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        // SQL Server seguro: eliminar primero si existe
        Schema::dropIfExists('historias_clinicas');

        Schema::create('historias_clinicas', function (Blueprint $table) {
            $table->id();

            // 🔗 RELACIONES
            $table->foreignId('paciente_id')
                  ->constrained('pacientes')
                  ->onDelete('no action'); // Paciente no se puede eliminar si hay historia

            $table->foreignId('cita_id')
                  ->nullable()
                  ->constrained('citas')
                  ->onDelete('no action');

            $table->foreignId('medico_id')
                  ->constrained('users')
                  ->onDelete('no action');

            $table->foreignId('registrado_por')
                  ->constrained('users')
                  ->onDelete('no action');

            // 🧠 DATOS CLÍNICOS
            $table->text('motivo_consulta');
            $table->text('antecedentes_personales')->nullable();
            $table->text('antecedentes_familiares')->nullable();
            $table->text('enfermedad_actual')->nullable();
            $table->text('examen_fisico')->nullable();

            // ❤️ SIGNOS VITALES
            $table->string('presion_arterial', 20)->nullable();
            $table->integer('frecuencia_cardiaca')->nullable();
            $table->integer('frecuencia_respiratoria')->nullable();
            $table->decimal('temperatura', 4,1)->nullable();
            $table->integer('saturacion_o2')->nullable();
            $table->decimal('peso', 5,2)->nullable();
            $table->decimal('talla', 5,2)->nullable();
            $table->decimal('imc', 5,2)->nullable();

            // 🩺 DIAGNÓSTICO
            $table->text('diagnostico_principal')->nullable();
            $table->text('diagnosticos_secundarios')->nullable();
            $table->string('cie10')->nullable();

            // 💊 TRATAMIENTO
            $table->text('tratamiento')->nullable();
            $table->text('indicaciones')->nullable();

            // 🧪 PROCEDIMIENTOS
            $table->text('procedimientos')->nullable();
            $table->text('examenes')->nullable();

            // 🔁 SEGUIMIENTO
            $table->text('evolucion')->nullable();
            $table->date('proxima_cita')->nullable();
            $table->boolean('alta_medica')->default(false);

            // 📋 CONTROL
            $table->enum('estado', ['abierta', 'seguimiento', 'cerrada'])->default('abierta');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historias_clinicas');
    }
};
