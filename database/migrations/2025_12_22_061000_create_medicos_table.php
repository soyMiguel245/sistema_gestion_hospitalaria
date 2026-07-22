<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('medicos', function (Blueprint $table) {
            $table->id();

            // 👇 CORREGIDO: se quita ->unique() de aquí. SQL Server no
            // permite más de un NULL en un índice único estándar, y
            // varios médicos pueden legítimamente no tener usuario ligado.
            // La unicidad real (un User no puede ser 2 médicos) se aplica
            // abajo con un índice único FILTRADO (solo sobre valores no nulos).
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->string('nombres');
            $table->string('apellidos');
            $table->string('dni')->unique();
            $table->string('cmp')->unique();

            $table->foreignId('especialidad_id')
                  ->constrained('especialidades')
                  ->onDelete('no action'); // 👈 antes era cascadeOnDelete(), ya corregido también

            $table->boolean('estado')->default(1);

            $table->timestamps();
        });

        // Índice único filtrado: solo exige unicidad cuando user_id NO es null.
        // Sintaxis específica de SQL Server (filtered index).
        DB::statement('CREATE UNIQUE INDEX medicos_user_id_unique ON medicos (user_id) WHERE user_id IS NOT NULL');
    }

    public function down(): void
    {
        Schema::dropIfExists('medicos');
    }
};