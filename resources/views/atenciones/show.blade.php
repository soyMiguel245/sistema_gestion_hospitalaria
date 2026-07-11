@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow border-0">
        <div class="card-header bg-info text-white">
            <h4>🧾 Ficha Clínica – Atención #{{ $atencion->id }}</h4>
        </div>
        <div class="card-body">

            <h5 class="text-primary">👤 Paciente</h5>
            <p>{{ $atencion->paciente->nombres ?? '-' }} {{ $atencion->paciente->apellidos ?? '' }}</p>

            <h5 class="text-success mt-3">👨‍⚕️ Médico Responsable</h5>
            <p>{{ $atencion->medico->nombres ?? '-' }} {{ $atencion->medico->apellidos ?? '' }}</p>

            <h5 class="text-danger mt-3">🩺 Diagnóstico</h5>
            <p>{{ $atencion->diagnostico ?? '-' }}</p>

            <h5 class="text-success mt-3">💊 Tratamiento</h5>
            <p>{{ $atencion->tratamiento ?? '-' }}</p>

            <h5 class="mt-3">📌 Estado</h5>
            <span class="badge {{ $atencion->estado == 'Atendido' ? 'bg-success' : 'bg-warning text-dark' }}">
                {{ $atencion->estado }}
            </span>

            <div class="mt-4">
                <a href="{{ route('atenciones.index') }}" class="btn btn-secondary">⬅ Volver</a>
            </div>

        </div>
    </div>
</div>
@endsection
