@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h4>Médicos</h4>
        <a href="{{ route('medicos.create') }}" class="btn btn-primary">
            ➕ Nuevo Médico
        </a>
    </div>

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
            @foreach($medicos as $medico)
            <tr>
                <td>{{ $medico->nombres }} {{ $medico->apellidos }}</td>
                <td>{{ $medico->dni }}</td>
                <td>{{ $medico->cmp }}</td>
                <td>{{ $medico->especialidad->nombre }}</td>
                <td>
                    <span class="badge {{ $medico->estado ? 'bg-success' : 'bg-danger' }}">
                        {{ $medico->estado ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('medicos.edit', $medico) }}" class="btn btn-warning btn-sm">✏️</a>
                    <form action="{{ route('medicos.destroy', $medico) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm">🗑️</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
