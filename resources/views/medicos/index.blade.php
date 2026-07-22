@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h4>👨‍⚕️ Médicos</h4>
        <a href="{{ route('medicos.create') }}" class="btn btn-primary">
            ➕ Nuevo Médico
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Nombre</th>
                <th>DNI</th>
                <th>CMP</th>
                <th>Especialidad</th>
                <th>Estado</th>
                <th width="160">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($medicos as $medico)
            <tr>
                {{-- 👇 CORREGIDO: el modelo Medico tiene 'nombres' y 'apellidos',
                     no un campo 'nombre' (singular). Por eso salía vacío. --}}
                <td>{{ $medico->nombres }} {{ $medico->apellidos }}</td>
                <td>{{ $medico->dni }}</td>
                <td>{{ $medico->cmp }}</td>
                <td>{{ $medico->especialidad->nombre ?? '—' }}</td>
                <td>
                    <span class="badge {{ $medico->estado ? 'bg-success' : 'bg-danger' }}">
                        {{ $medico->estado ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('medicos.edit', $medico) }}" class="btn btn-warning btn-sm">✏️</a>
                    <form action="{{ route('medicos.destroy', $medico) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm"
                                onclick="return confirm('¿Eliminar este médico?')">🗑️</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted">
                    No hay médicos registrados
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection