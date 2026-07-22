<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('archivos_medicos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('atencion_medica_id')
                  ->constrained('atenciones_medicas')
                  ->cascadeOnDelete(); // si se borra la atención, se borran sus archivos

            // Distingue si es un examen (pdf/jpg/png) o una imagen médica (jpg/png)
            $table->enum('tipo', ['examen', 'imagen']);

            $table->string('ruta');           // ej: examenes/abc123.pdf
            $table->string('nombre_original'); // nombre real que subió el usuario
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('tamano_bytes')->nullable();

            $table->foreignId('subido_por')
                  ->constrained('users')
                  ->onDelete('no action');

            $table->timestamps();
        });

        // Elimina las columnas viejas de JSON; los datos que tuvieras ahí
        // no se migran automáticamente (ver nota en el mensaje del chat).
        Schema::table('atenciones_medicas', function (Blueprint $table) {
            $table->dropColumn(['examenes_adjuntos', 'imagenes_medicas']);
        });
    }

    public function down(): void
    {
        Schema::table('atenciones_medicas', function (Blueprint $table) {
            $table->json('examenes_adjuntos')->nullable();
            $table->json('imagenes_medicas')->nullable();
        });

        Schema::dropIfExists('archivos_medicos');
    }
};