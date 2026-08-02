<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnosticos', function (Blueprint $table) {
            $table->id();

            // 🔗 Relación con Atención Médica
            $table->foreignId('atencion_medica_id')
                ->constrained('atenciones_medicas')
                ->cascadeOnDelete();

            // Datos del diagnóstico
            $table->text('descripcion');
            $table->enum('tipo', ['Principal', 'Secundario'])->default('Secundario');
            $table->string('cie10', 10)->nullable();
            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnosticos');
    }
};
