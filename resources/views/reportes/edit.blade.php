<!-- resources/views/reportes/edit.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Editar Reporte</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>¡Ups!</strong> Revisa los campos obligatorios.
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('reportes.update', $reporte->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('reportes._form', ['reporte' => $reporte])
        <button type="submit" class="btn btn-primary mt-3">Actualizar</button>
        <a href="{{ route('reportes.index') }}" class="btn btn-secondary mt-3">Cancelar</a>
    </form>
</div>
@endsection
