<?php

namespace App\Rules;

use App\Models\Cita;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Verifica que el médico no tenga otra cita cuyo rango de horario se
 * solape con la nueva. Usa fecha_hora + duracion (minutos) de AMBAS citas,
 * no solo coincidencia exacta de fecha_hora — así una cita de 10:00-10:30
 * sí choca con otra de 10:15-10:45, aunque no empiecen a la misma hora.
 *
 * Excluye citas 'Cancelada' y 'No asistió' (esos horarios quedan libres),
 * y excluye la cita actual al editar (para no chocar contra sí misma).
 */
class MedicoDisponible implements ValidationRule
{
    public function __construct(
        private ?int $medicoId,
        private ?string $fechaHora,
        private int $duracionMinutos = 30,
        private ?int $ignorarCitaId = null,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->medicoId || ! $this->fechaHora) {
            return; // otras reglas (required) ya se encargan de esto
        }

        $inicioNueva = \Carbon\Carbon::parse($this->fechaHora);
        $finNueva = $inicioNueva->copy()->addMinutes($this->duracionMinutos);

        $conflicto = Cita::where('medico_id', $this->medicoId)
            ->whereNotIn('estado', ['Cancelada', 'No asistió'])
            ->when($this->ignorarCitaId, fn ($q) => $q->where('id', '!=', $this->ignorarCitaId))
            ->get()
            ->first(function (Cita $citaExistente) use ($inicioNueva, $finNueva) {
                $inicioExistente = \Carbon\Carbon::parse($citaExistente->fecha_hora);
                $finExistente = $inicioExistente->copy()->addMinutes($citaExistente->duracion ?? 30);

                // Se solapan si una empieza antes de que la otra termine, en ambos sentidos.
                return $inicioNueva->lt($finExistente) && $finNueva->gt($inicioExistente);
            });

        if ($conflicto) {
            $fail("El médico ya tiene una cita programada que choca con este horario (cita #{$conflicto->id}, {$conflicto->fecha_hora}).");
        }
    }
}
