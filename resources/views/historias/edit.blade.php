@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4">✏️ Editar Historia Clínica</h4>

    <form method="POST" action="{{ route('historias.update', $historia->id) }}">
        @csrf
        @method('PUT')

        {{-- DATOS DEL PACIENTE --}}
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                🧍 Datos del Paciente
            </div>
            <div class="card-body row">
                <div class="col-md-6">
                    <label class="form-label">Paciente *</label>
                    <select name="paciente_id" class="form-select" required>
                        @foreach($pacientes as $paciente)
                            <option value="{{ $paciente->id }}"
                                {{ $historia->paciente_id == $paciente->id ? 'selected' : '' }}>
                                {{ $paciente->nombres }} {{ $paciente->apellidos }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- ATENCIÓN MÉDICA --}}
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                🩺 Atención Médica
            </div>
            <div class="card-body">
                <label>Motivo de Consulta *</label>
                <textarea name="motivo_consulta" class="form-control mb-2" required>
{{ old('motivo_consulta', $historia->motivo_consulta) }}</textarea>

                <label>Antecedentes Personales</label>
                <textarea name="antecedentes_personales" class="form-control mb-2">
{{ old('antecedentes_personales', $historia->antecedentes_personales) }}</textarea>

                <label>Antecedentes Familiares</label>
                <textarea name="antecedentes_familiares" class="form-control mb-2">
{{ old('antecedentes_familiares', $historia->antecedentes_familiares) }}</textarea>

                <label>Enfermedad Actual</label>
                <textarea name="enfermedad_actual" class="form-control">
{{ old('enfermedad_actual', $historia->enfermedad_actual) }}</textarea>
            </div>
        </div>

        {{-- SIGNOS VITALES --}}
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">
                ❤️ Signos Vitales
            </div>
            <div class="card-body row">
                <div class="col"><input name="presion_arterial" class="form-control" placeholder="PA"
                    value="{{ old('presion_arterial', $historia->presion_arterial) }}"></div>
                <div class="col"><input name="frecuencia_cardiaca" class="form-control" placeholder="FC"
                    value="{{ old('frecuencia_cardiaca', $historia->frecuencia_cardiaca) }}"></div>
                <div class="col"><input name="frecuencia_respiratoria" class="form-control" placeholder="FR"
                    value="{{ old('frecuencia_respiratoria', $historia->frecuencia_respiratoria) }}"></div>
                <div class="col"><input name="temperatura" class="form-control" placeholder="°C"
                    value="{{ old('temperatura', $historia->temperatura) }}"></div>
                <div class="col"><input name="saturacion_o2" class="form-control" placeholder="SpO₂"
                    value="{{ old('saturacion_o2', $historia->saturacion_o2) }}"></div>
            </div>
        </div>

        {{-- DIAGNÓSTICO Y TRATAMIENTO --}}
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                🧠 Diagnóstico y Tratamiento
            </div>
            <div class="card-body">
                <label>Diagnóstico</label>
                <textarea name="diagnostico_principal" class="form-control mb-2">
{{ old('diagnostico_principal', $historia->diagnostico_principal) }}</textarea>

                <label>Tratamiento</label>
                <textarea name="tratamiento" class="form-control mb-2">
{{ old('tratamiento', $historia->tratamiento) }}</textarea>

                <label>Indicaciones</label>
                <textarea name="indicaciones" class="form-control">
{{ old('indicaciones', $historia->indicaciones) }}</textarea>
            </div>
        </div>

        <button class="btn btn-warning">
            💾 Actualizar Historia Clínica
        </button>
        <a href="{{ route('historias.index') }}" class="btn btn-secondary">
            Cancelar
        </a>
    </form>
</div>
@endsection
