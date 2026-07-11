<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();

            $table->string('tipo'); // Tipo de reporte (PDF, Excel, etc.)
            $table->string('reporte'); // Nombre del reporte
            $table->foreignId('usuario_id')
                  ->constrained('users')   // FK hacia tabla users
                  ->cascadeOnDelete();     // Si el usuario se elimina, sus reportes también

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes');
    }
};
