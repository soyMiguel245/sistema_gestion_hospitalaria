@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h4>👨‍⚕️ Médicos</h4>
        <a href="{{ route('medicos.create') }}" class="btn btn-primary">
            ➕ Nuevo Médico
        </a>
    </div>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Nombre</th>
                <th>Especialidad</th>
                <th>Estado</th>
                <th width="120">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($medicos as $medico)
            <tr>
                <td>{{ $medico->nombre }}</td>
                <td>{{ $medico->especialidad->nombre ?? '—' }}</td>
                <td>
                    <span class="badge {{ $medico->estado ? 'bg-success' : 'bg-danger' }}">
                        {{ $medico->estado ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('medicos.edit', $medico) }}" class="btn btn-warning btn-sm">✏️</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">
                    No hay médicos registrados
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
