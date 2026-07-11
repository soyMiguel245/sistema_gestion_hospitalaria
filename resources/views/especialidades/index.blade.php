@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h4>Especialidades Médicas</h4>
        <a href="{{ route('especialidades.create') }}" class="btn btn-primary">
            ➕ Nueva Especialidad
        </a>
    </div>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th width="160">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($especialidades as $esp)
            <tr>
                <td>{{ $esp->nombre }}</td>
                <td>{{ $esp->descripcion }}</td>
                <td>
                    <span class="badge {{ $esp->estado ? 'bg-success' : 'bg-danger' }}">
                        {{ $esp->estado ? 'Activa' : 'Inactiva' }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('especialidades.edit', $esp) }}" class="btn btn-warning btn-sm">✏️</a>
                    <form action="{{ route('especialidades.destroy', $esp) }}" method="POST" class="d-inline">
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
