@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow border-0">
        <div class="card-header bg-warning text-white">
            <h4 class="mb-0">✏️ Editar Atención Médica</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('atenciones.update', $atencion->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- PACIENTE -->
                <div class="mb-3">
                    <label class="form-label">Paciente</label>
                    <select name="paciente_id" class="form-select" required>
                        <option value="">-- Seleccione --</option>
                        @foreach($pacientes as $paciente)
                            <option value="{{ $paciente->id }}"
                                {{ $paciente->id == $atencion->paciente_id ? 'selected' : '' }}>
                                {{ $paciente->nombres }} {{ $paciente->apellidos }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- MÉDICO -->
                <div class="mb-3">
                    <label class="form-label">Médico Responsable</label>
                    <select name="medico_id" class="form-select" required>
                        <option value="">-- Seleccione --</option>
                        @foreach($medicos as $medico)
                            <option value="{{ $medico->id }}"
                                {{ $medico->id == $atencion->medico_id ? 'selected' : '' }}>
                                {{ $medico->nombres }} {{ $medico->apellidos }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- DIAGNÓSTICO -->
                <div class="mb-3">
                    <label class="form-label">Diagnóstico</label>
                    <textarea name="diagnostico" class="form-control" rows="3">{{ old('diagnostico', $atencion->diagnostico) }}</textarea>
                </div>

                <!-- TRATAMIENTO -->
                <div class="mb-3">
                    <label class="form-label">Tratamiento</label>
                    <textarea name="tratamiento" class="form-control" rows="3">{{ old('tratamiento', $atencion->tratamiento) }}</textarea>
                </div>

                <!-- ESTADO -->
                <div class="mb-3">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select" required>
                        <option value="Pendiente" {{ $atencion->estado == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="En Progreso" {{ $atencion->estado == 'En Progreso' ? 'selected' : '' }}>En Progreso</option>
                        <option value="Atendido" {{ $atencion->estado == 'Atendido' ? 'selected' : '' }}>Atendido</option>
                        <option value="Derivado" {{ $atencion->estado == 'Derivado' ? 'selected' : '' }}>Derivado</option>
                        <option value="Alta" {{ $atencion->estado == 'Alta' ? 'selected' : '' }}>Alta</option>
                    </select>
                </div>

                <div class="mt-4 text-end">
                    <a href="{{ route('atenciones.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button class="btn btn-primary px-4">Actualizar</button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
