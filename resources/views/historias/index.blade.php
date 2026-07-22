@extends('layouts.app')

@section('content')
<div class="container-fluid">

{{-- TÍTULO --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-primary">
        📁 Historias Clínicas
    </h3>
    {{-- 👇 Se quitó el botón "Nueva Historia Clínica": ya no se crea por
         separado, se genera automáticamente al registrar una Atención Médica --}}
</div>

{{-- BUSCADOR --}}
<div class="card shadow-sm mb-4 border-0">
    <div class="card-body bg-light rounded">
        <form method="GET" action="{{ route('historias.index') }}">
            <div class="row g-2 align-items-center">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-primary text-white">
                            🔍
                        </span>
                        <input
                            type="text"
                            name="buscar"
                            class="form-control"
                            placeholder="Buscar por nombre, apellido o DNI del paciente..."
                            value="{{ request('buscar') }}"
                        >
                    </div>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary">
                        🔎 Buscar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- TABLA --}}
<div class="card shadow-lg border-0">
    <div class="card-header bg-primary text-white fw-bold">
        📋 Pacientes con Historial Clínico
    </div>

    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead style="background-color:#e9f2ff;" class="text-primary text-center fw-semibold">
                <tr>
                    <th>#</th>
                    <th>👤 Paciente</th>
                    <th>🪪 DNI</th>
                    <th>📁 N° Historia Clínica</th>
                    <th>🩺 Atenciones Registradas</th>
                    <th style="width: 120px;">⚙️ Acciones</th>
                </tr>
            </thead>

            <tbody>
                @forelse($pacientes as $paciente)
                <tr>
                    <td class="text-center fw-bold">{{ $loop->iteration }}</td>

                    <td>
                        👤 {{ $paciente->nombres }} {{ $paciente->apellidos }}
                    </td>

                    <td class="text-center">
                        {{ $paciente->dni }}
                    </td>

                    <td class="text-center">
                        {{ $paciente->numero_historia_clinica }}
                    </td>

                    <td class="text-center">
                        <span class="badge bg-info text-dark">
                            {{ $paciente->atenciones_medicas_count }}
                        </span>
                    </td>

                    <td class="text-center">
                        <a href="{{ route('historias.show', $paciente) }}"
                           class="btn btn-sm btn-primary"
                           title="Ver Expediente Completo">
                            👁 Ver
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        📂 No hay pacientes con historial clínico registrado
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

</div>
@endsection