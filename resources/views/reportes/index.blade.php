<!-- resources/views/reportes/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Reportes</h2>

    <a href="{{ route('reportes.create') }}" class="btn btn-primary mb-3">Nuevo Reporte</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Usuario</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportes as $reporte)
                <tr>
                    <td>{{ $reporte->id }}</td>
                    <td>{{ $reporte->reporte }}</td>
                    <td>{{ $reporte->tipo }}</td>
                    <td>{{ $reporte->usuario->name }}</td>
                    <td>{{ $reporte->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('reportes.show', $reporte->id) }}" class="btn btn-info btn-sm">Ver</a>
                        <a href="{{ route('reportes.edit', $reporte->id) }}" class="btn btn-warning btn-sm">Editar</a>
                        <form action="{{ route('reportes.destroy', $reporte->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este reporte?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $reportes->links() }}
</div>
@endsection
