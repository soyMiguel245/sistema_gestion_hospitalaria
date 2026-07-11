@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- ================= TÍTULO ================= --}}
    <div class="mb-4 p-3 rounded shadow-sm"
         style="background:#0d6efd;color:white">
        <h4 class="fw-bold mb-0">🗂️ Nueva Historia Clínica</h4>
        <small>Registro clínico completo del paciente</small>
    </div>

    <form method="POST" action="{{ route('historias.store') }}">
        @csrf

        {{-- ================= DATOS DEL PACIENTE ================= --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header fw-semibold"
                 style="background:#eef4ff;color:#084298">
                👤 Datos del Paciente
            </div>

            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="fw-semibold small">Paciente *</label>
                        <select name="paciente_id" class="form-select form-select-sm" required>
                            <option value="">Seleccione paciente</option>
                            @foreach($pacientes as $paciente)
                                <option value="{{ $paciente->id }}">
                                    {{ $paciente->nombres }} {{ $paciente->apellidos }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="fw-semibold small">Cita (opcional)</label>
                        <select name="cita_id" class="form-select form-select-sm">
                            <option value="">Sin cita</option>
                            @foreach($citas as $cita)
                                <option value="{{ $cita->id }}">
                                    {{ $cita->fecha }} - {{ $cita->hora }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>
        </div>

        {{-- ================= MOTIVO ================= --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header fw-semibold"
                 style="background:#f1f3f5;color:#495057">
                📝 Motivo de Consulta
            </div>

            <div class="card-body">
                <textarea name="motivo_consulta"
                          class="form-control form-control-sm"
                          rows="3"
                          placeholder="Describa el motivo principal de la consulta"
                          required></textarea>
            </div>
        </div>

        {{-- ================= ANTECEDENTES ================= --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header fw-semibold"
                 style="background:#eef4ff;color:#084298">
                📚 Antecedentes
            </div>

            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <div class="p-3 rounded border-start border-4"
                             style="background:#ffffff;border-color:#0d6efd">
                            <label class="fw-semibold small text-primary">
                                👤 Personales
                            </label>
                            <textarea name="antecedentes_personales"
                                      class="form-control form-control-sm"
                                      rows="4"
                                      placeholder="Cirugías, alergias, enfermedades previas..."></textarea>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 rounded border-start border-4"
                             style="background:#ffffff;border-color:#20c997">
                            <label class="fw-semibold small text-success">
                                👪 Familiares
                            </label>
                            <textarea name="antecedentes_familiares"
                                      class="form-control form-control-sm"
                                      rows="4"
                                      placeholder="HTA, diabetes, cáncer, etc."></textarea>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ================= ENFERMEDAD ACTUAL ================= --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header fw-semibold"
                 style="background:#f1f3f5;color:#495057">
                🦠 Enfermedad Actual
            </div>

            <div class="card-body">
                <div class="p-3 rounded border-start border-4"
                     style="background:#ffffff;border-color:#6c757d">
                    <textarea name="enfermedad_actual"
                              class="form-control form-control-sm"
                              rows="4"
                              placeholder="Inicio, evolución y síntomas actuales"></textarea>
                </div>
            </div>
        </div>

        {{-- ================= SIGNOS VITALES ================= --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header fw-semibold"
                 style="background:#ffeaea;color:#842029">
                ❤️ Signos Vitales
            </div>

            <div class="card-body">
                <div class="row g-3 text-center">

                    @php
                        $signos = [
                            ['PA','presion_arterial','mmHg'],
                            ['FC','frecuencia_cardiaca','lpm'],
                            ['FR','frecuencia_respiratoria','rpm'],
                            ['Temp','temperatura','°C'],
                            ['SpO₂','saturacion_o2','%'],
                        ];
                    @endphp

                    @foreach($signos as $s)
                        <div class="col-md">
                            <div class="p-3 rounded border bg-white">
                                <div class="fw-semibold text-danger small">
                                    {{ $s[0] }}
                                </div>
                                <input name="{{ $s[1] }}"
                                       class="form-control form-control-sm text-center"
                                       placeholder="{{ $s[2] }}">
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>

        {{-- ================= DIAGNÓSTICO Y TRATAMIENTO ================= --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header fw-semibold"
                 style="background:#e7f1ff;color:#084298">
                🩺 Diagnóstico y Tratamiento
            </div>

            <div class="card-body">

                <div class="mb-3">
                    <label class="fw-semibold small">Diagnóstico</label>
                    <textarea name="diagnostico_principal"
                              class="form-control form-control-sm"
                              rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold small">Tratamiento</label>
                    <textarea name="tratamiento"
                              class="form-control form-control-sm"
                              rows="3"></textarea>
                </div>

                <div>
                    <label class="fw-semibold small">Indicaciones</label>
                    <textarea name="indicaciones"
                              class="form-control form-control-sm"
                              rows="3"></textarea>
                </div>

            </div>
        </div>

        {{-- ================= BOTÓN ================= --}}
        <div class="text-end mb-4">
            <button class="btn btn-success btn-lg shadow-sm">
                💾 Guardar Historia Clínica
            </button>
        </div>

    </form>
</div>
@endsection
