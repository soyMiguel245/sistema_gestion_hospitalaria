<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

// Esta migración elimina `historias_clinicas` porque duplicaba, campo por campo,
// la información que ya vive en `atenciones_medicas` + `diagnosticos`.
//
// En un hospital real, la "historia clínica" NO es un formulario aparte que se
// llena en cada consulta: es el expediente permanente del paciente (ya identificado
// por `pacientes.numero_historia_clinica`), compuesto por la línea de tiempo de
// todas sus atenciones médicas. Por eso no necesita tabla propia: se arma
// consultando $paciente->atencionesMedicas()->with('diagnosticos').
//
// ⚠️ Corre esto SOLO si confirmaste que no hay datos reales de historias_clinicas
// que necesites conservar (según la conversación, solo tenías datos de prueba).
return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('historias_clinicas');
    }

    public function down(): void
    {
        // No se recrea automáticamente; si necesitas revertir,
        // restaura la migración original de historias_clinicas.
    }
};