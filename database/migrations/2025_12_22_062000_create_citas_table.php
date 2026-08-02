<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Primero eliminamos la tabla si existe
        Schema::dropIfExists('citas');

        // Creamos la tabla
        Schema::create('citas', function (Blueprint $table) {
            $table->id();

            // IDENTIFICACIÓN
            $table->string('codigo_cita')->unique();

            // RELACIONES
            $table->foreignId('paciente_id')
                ->constrained('pacientes')
                ->cascadeOnDelete(); // Seguro, eliminar paciente elimina sus citas

            $table->foreignId('medico_id')
                ->constrained('medicos')
                ->onDelete('no action'); // ⚠ Evita multiple cascade paths

            $table->foreignId('especialidad_id')
                ->constrained('especialidades')
                ->onDelete('no action'); // ⚠ Evita multiple cascade paths

            // FECHA Y TIEMPO
            $table->dateTime('fecha_hora');
            $table->enum('turno', ['Mañana', 'Tarde', 'Noche']);
            $table->integer('duracion')->default(30);
            $table->string('consultorio')->nullable();

            // ADMINISTRATIVO
            $table->enum('tipo_cita', ['Consulta', 'Emergencia', 'Control', 'Procedimiento']);
            $table->enum('origen', ['Presencial', 'Web', 'Referido']);
            $table->enum('area_servicio', ['Consulta Externa', 'Emergencias', 'Hospitalización']);
            $table->enum('prioridad', ['Normal', 'Urgente', 'Emergente'])->default('Normal');

            // CLÍNICO
            $table->string('motivo');
            $table->text('motivo_clinico')->nullable();
            $table->text('observaciones_medicas')->nullable();

            // FACTURACIÓN
            $table->enum('tipo_paciente', ['Particular', 'Seguro', 'Convenio']);
            $table->decimal('costo', 8, 2)->nullable();
            $table->enum('estado_pago', ['Pendiente', 'Pagado', 'Exonerado'])->default('Pendiente');
            $table->string('numero_autorizacion')->nullable();

            // SEGUIMIENTO
            $table->enum('estado', [
                'Programada',
                'Confirmada',
                'En espera',
                'En atención',
                'Atendida',
                'Cancelada',
                'Reprogramada',
                'No asistió',
            ])->default('Programada');

            $table->boolean('confirmada')->default(false);
            $table->string('motivo_cancelacion')->nullable();
            $table->string('motivo_reprogramacion')->nullable();

            // AUDITORÍA
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
