@extends('layouts.app')

@section('content')
<div class="container">

    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            📄 Historia Clínica
        </div>

        <div class="card-body">

            {{-- CABECERA --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <b>Paciente:</b>
                    {{ $historia->paciente->nombres }}
                    {{ $historia->paciente->apellidos }}
                </div>
                <div class="col-md-6">
                    <b>Médico:</b> {{ $historia->medico->name }}
                </div>
                <div class="col-md-6 mt-2">
                    <b>Fecha:</b> {{ $historia->created_at->format('d/m/Y H:i') }}
                </div>
                <div class="col-md-6 mt-2">
                    <b>Estado:</b>
                    <span class="badge {{ $historia->estado == 'cerrada' ? 'bg-danger' : 'bg-success' }}">
                        {{ strtoupper($historia->estado) }}
                    </span>
                </div>
            </div>

            <hr>

            {{-- MOTIVO --}}
            <h6>🩺 Motivo de Consulta</h6>
            <p>{{ $historia->motivo_consulta }}</p>

            {{-- ANTECEDENTES --}}
            <h6>📋 Antecedentes</h6>
            <p><b>Personales:</b> {{ $historia->antecedentes_personales ?? 'No registra' }}</p>
            <p><b>Familiares:</b> {{ $historia->antecedentes_familiares ?? 'No registra' }}</p>

            {{-- ENFERMEDAD --}}
            <h6>🧠 Enfermedad Actual</h6>
            <p>{{ $historia->enfermedad_actual ?? 'No registra' }}</p>

            {{-- SIGNOS VITALES --}}
            <h6>❤️ Signos Vitales</h6>
            <table class="table table-sm table-bordered">
                <tr>
                    <th>PA</th>
                    <th>FC</th>
                    <th>FR</th>
                    <th>Temp °C</th>
                    <th>SpO₂</th>
                </tr>
                <tr>
                    <td>{{ $historia->presion_arterial }}</td>
                    <td>{{ $historia->frecuencia_cardiaca }}</td>
                    <td>{{ $historia->frecuencia_respiratoria }}</td>
                    <td>{{ $historia->temperatura }}</td>
                    <td>{{ $historia->saturacion_o2 }}</td>
                </tr>
            </table>

            {{-- DIAGNÓSTICO --}}
            <h6>🧾 Diagnóstico</h6>
            <p>{{ $historia->diagnostico_principal }}</p>

            {{-- TRATAMIENTO --}}
            <h6>💊 Tratamiento</h6>
            <p>{{ $historia->tratamiento }}</p>

            {{-- INDICACIONES --}}
            <h6>📌 Indicaciones</h6>
            <p>{{ $historia->indicaciones }}</p>

        </div>
    </div>

    <a href="{{ route('historias.index') }}" class="btn btn-secondary">
        ⬅ Volver
    </a>

</div>
@endsection
