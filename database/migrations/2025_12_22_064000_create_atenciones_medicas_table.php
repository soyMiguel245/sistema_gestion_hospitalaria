<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('atenciones_medicas', function (Blueprint $table) {
            $table->id();

            // 🔗 Relaciones
            $table->foreignId('paciente_id')
                  ->constrained('pacientes')
                  ->onDelete('no action');

            $table->foreignId('cita_id')
                  ->nullable()
                  ->constrained('citas')
                  ->onDelete('set null');

            $table->foreignId('medico_id')
                  ->constrained('medicos') // ✅ Apunta a tabla medicos
                  ->onDelete('no action');

            $table->foreignId('registrado_por')
                  ->constrained('users')
                  ->onDelete('no action');

            // Información clínica
            $table->text('motivo_consulta');
            $table->text('diagnostico')->nullable();
            $table->text('tratamiento')->nullable();
            $table->text('procedimientos')->nullable();
            $table->text('indicaciones')->nullable();
            $table->text('observaciones')->nullable();

            // Signos vitales
            $table->string('presion_arterial', 20)->nullable();
            $table->integer('frecuencia_cardiaca')->nullable();
            $table->integer('frecuencia_respiratoria')->nullable();
            $table->decimal('temperatura', 4, 1)->nullable();
            $table->integer('saturacion_o2')->nullable();
            $table->decimal('peso', 5, 2)->nullable();
            $table->decimal('talla', 5, 2)->nullable();
            $table->decimal('imc', 5, 2)->nullable();

            // Archivos y exámenes
            $table->json('examenes_adjuntos')->nullable();
            $table->json('imagenes_medicas')->nullable();

            // Facturación y seguros
            $table->enum('tipo_paciente', ['Particular', 'Seguro', 'Convenio'])->default('Particular');
            $table->decimal('costo', 8, 2)->nullable();
            $table->decimal('descuento', 8, 2)->default(0);
            $table->enum('estado_pago', ['Pendiente', 'Pagado', 'Exonerado'])->default('Pendiente');
            $table->string('numero_autorizacion')->nullable();

            // Seguimiento
            $table->enum('estado', ['Pendiente', 'En Progreso', 'Atendido', 'Derivado', 'Alta'])->default('Pendiente');
            $table->date('proxima_cita')->nullable();
            $table->boolean('alta_medica')->default(false);

            // Auditoría
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atenciones_medicas');
    }
};
