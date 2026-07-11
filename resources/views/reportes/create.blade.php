<!-- resources/views/reportes/create.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Crear Reporte</h2>

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

    <form action="{{ route('reportes.store') }}" method="POST">
        @csrf
        @include('reportes._form', ['reporte' => null])
        <button type="submit" class="btn btn-success mt-3">Guardar</button>
        <a href="{{ route('reportes.index') }}" class="btn btn-secondary mt-3">Cancelar</a>
    </form>
</div>
@endsection
