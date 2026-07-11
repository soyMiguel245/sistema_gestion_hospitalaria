@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4">Registrar Médico</h4>

    <form action="{{ route('medicos.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Nombres</label>
                <input type="text" name="nombres" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>Apellidos</label>
                <input type="text" name="apellidos" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>DNI</label>
                <input type="text" name="dni" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>CMP</label>
                <input type="text" name="cmp" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>Especialidad</label>
                <select name="especialidad_id" class="form-select" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($especialidades as $especialidad)
                        <option value="{{ $especialidad->id }}">
                            {{ $especialidad->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label>Estado</label>
                <select name="estado" class="form-select" required>
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>
            </div>
        </div>

        <button class="btn btn-success">💾 Guardar Médico</button>
        <a href="{{ route('medicos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
