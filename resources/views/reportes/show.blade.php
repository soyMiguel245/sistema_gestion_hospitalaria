<!-- resources/views/reportes/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Detalle del Reporte</h2>

    <div class="card">
        <div class="card-body">
            <p><strong>ID:</strong> {{ $reporte->id }}</p>
            <p><strong>Nombre:</strong> {{ $reporte->reporte }}</p>
            <p><strong>Tipo:</strong> {{ $reporte->tipo }}</p>
            <p><strong>Descripción:</strong> {{ $reporte->descripcion ?? '-' }}</p>
            <p><strong>Creado por:</strong> {{ $reporte->usuario->name }}</p>
            <p><strong>Fecha:</strong> {{ $reporte->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <a href="{{ route('reportes.index') }}" class="btn btn-secondary mt-3">Volver</a>
</div>
@endsection
