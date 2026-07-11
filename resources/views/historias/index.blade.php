@extends('layouts.app')

@section('content')
<div class="container-fluid">

{{-- TÍTULO --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-primary">
        📁 Gestión de Historias Clínicas
    </h3>

    <a href="{{ route('historias.create') }}" class="btn btn-success">
        ➕ Nueva Historia Clínica
    </a>
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
                            placeholder="Buscar por paciente o médico..."
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
        📋 Registro de Historias Clínicas
    </div>

    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
        <thead style="background-color:#e9f2ff;" class="text-primary text-center fw-semibold">
    <tr>
        <th>#</th>
        <th>
            👤 Paciente
        </th>
        <th>
            🩺 Médico
        </th>
        <th>
            🟢 Estado
        </th>
        <th>
            📅 Fecha
        </th>
        <th style="width: 180px;">
            ⚙️ Acciones
        </th>
    </tr>
</thead>


            <tbody>
                @forelse($historias as $historia)
                <tr>
                    <td class="text-center fw-bold">{{ $historia->id }}</td>

                    <td>
                        👤 {{ $historia->paciente->nombres }}
                        {{ $historia->paciente->apellidos }}
                    </td>

                    <td>
                        🩺 {{ $historia->medico->name }}
                    </td>

                    <td class="text-center">
                        @if($historia->estado == 'cerrada')
                            <span class="badge bg-danger">
                                🔒 Cerrada
                            </span>
                        @else
                            <span class="badge bg-success">
                                🟢 Activa
                            </span>
                        @endif
                    </td>

                    <td class="text-center">
                        📅 {{ $historia->created_at->format('d/m/Y') }}
                    </td>

                    <td class="text-center">
                        <a href="{{ route('historias.show', $historia) }}"
                           class="btn btn-sm btn-primary"
                           title="Ver Historia">
                            👁
                        </a>

                        @if($historia->estado != 'cerrada')
                        <form action="{{ route('historias.cerrar', $historia) }}"
                              method="POST"
                              class="d-inline">
                            @csrf
                            @method('PUT')
                            <button class="btn btn-sm btn-danger"
                                    title="Cerrar Historia"
                                    onclick="return confirm('¿Cerrar historia clínica?')">
                                🔒
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        📂 No hay historias clínicas registradas
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

</div>
@endsection
