@extends('layouts.app')

@section('content')
<div class="container">

    {{-- CABECERA DEL PACIENTE --}}
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            📄 Expediente Clínico
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <b>Paciente:</b> {{ $paciente->nombres }} {{ $paciente->apellidos }}
                </div>
                <div class="col-md-6">
                    <b>DNI:</b> {{ $paciente->dni }}
                </div>
                <div class="col-md-6 mt-2">
                    <b>N° Historia Clínica:</b> {{ $paciente->numero_historia_clinica }}
                </div>
                <div class="col-md-6 mt-2">
                    <b>Tipo de Sangre:</b> {{ $paciente->tipo_sangre ?? 'No registra' }}
                </div>
                <div class="col-md-12 mt-2">
                    <b>Alergias:</b> {{ $paciente->alergias ?? 'No registra' }}
                </div>
            </div>
        </div>
    </div>

    <h5 class="fw-bold text-primary mb-3">
        🕐 Línea de Tiempo — {{ $atenciones->count() }} atención(es) registrada(s)
    </h5>

    {{-- LÍNEA DE TIEMPO DE ATENCIONES --}}
    @forelse($atenciones as $atencion)
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center"
             style="background:#eef4ff;color:#084298">
            <span>
                📅 {{ $atencion->created_at->format('d/m/Y H:i') }}
                &nbsp;|&nbsp;
                🩺 {{ $atencion->medico->nombres }} {{ $atencion->medico->apellidos }}
            </span>
            <span class="badge {{ $atencion->estado == 'Alta' ? 'bg-danger' : 'bg-success' }}">
                {{ strtoupper($atencion->estado) }}
            </span>
        </div>

        <div class="card-body">

            <h6>🩺 Motivo de Consulta</h6>
            <p>{{ $atencion->motivo_consulta }}</p>

            {{-- SIGNOS VITALES --}}
            <h6>❤️ Signos Vitales</h6>
            <table class="table table-sm table-bordered">
                <tr>
                    <th>PA</th>
                    <th>FC</th>
                    <th>FR</th>
                    <th>Temp °C</th>
                    <th>SpO₂</th>
                    <th>Peso</th>
                    <th>Talla</th>
                    <th>IMC</th>
                </tr>
                <tr>
                    <td>{{ $atencion->presion_arterial ?? '-' }}</td>
                    <td>{{ $atencion->frecuencia_cardiaca ?? '-' }}</td>
                    <td>{{ $atencion->frecuencia_respiratoria ?? '-' }}</td>
                    <td>{{ $atencion->temperatura ?? '-' }}</td>
                    <td>{{ $atencion->saturacion_o2 ?? '-' }}</td>
                    <td>{{ $atencion->peso ?? '-' }}</td>
                    <td>{{ $atencion->talla ?? '-' }}</td>
                    <td>{{ $atencion->imc ?? '-' }}</td>
                </tr>
            </table>

            {{-- DIAGNÓSTICOS (tabla normalizada, puede haber varios) --}}
            <h6>🧾 Diagnóstico(s)</h6>
            @forelse($atencion->diagnosticos as $diagnostico)
                <p class="mb-1">
                    <span class="badge bg-secondary">{{ $diagnostico->tipo }}</span>
                    {{ $diagnostico->descripcion }}
                    @if($diagnostico->cie10)
                        <small class="text-muted">(CIE-10: {{ $diagnostico->cie10 }})</small>
                    @endif
                </p>
            @empty
                <p class="text-muted">No se registraron diagnósticos estructurados para esta atención.</p>
            @endforelse
            @if($atencion->diagnostico)
                <p class="mt-2"><b>Nota diagnóstica:</b> {{ $atencion->diagnostico }}</p>
            @endif

            {{-- TRATAMIENTO --}}
            <h6>💊 Tratamiento</h6>
            <p>{{ $atencion->tratamiento ?? 'No registra' }}</p>

            {{-- INDICACIONES --}}
            <h6>📌 Indicaciones</h6>
            <p>{{ $atencion->indicaciones ?? 'No registra' }}</p>

            @if($atencion->cita->exists)
            <small class="text-muted">
            🗓 Originada por la cita #{{ $atencion->cita->codigo_cita }}
            ({{ $atencion->cita->fecha_hora->format('d/m/Y H:i') }})
            </small>
@endif

        </div>
    </div>
    @empty
    <div class="alert alert-light text-center text-muted">
        📂 Este paciente aún no tiene atenciones médicas registradas.
    </div>
    @endforelse

    <a href="{{ route('historias.index') }}" class="btn btn-secondary">
        ⬅ Volver
    </a>

</div>
@endsection