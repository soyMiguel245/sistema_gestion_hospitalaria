@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4">✏️ Editar Médico</h4>

    <form action="{{ route('medicos.update', $medico) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Nombres</label>
                <input type="text" name="nombres" class="form-control"
                       value="{{ old('nombres', $medico->nombres) }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>Apellidos</label>
                <input type="text" name="apellidos" class="form-control"
                       value="{{ old('apellidos', $medico->apellidos) }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>DNI</label>
                <input type="text" name="dni" class="form-control"
                       value="{{ old('dni', $medico->dni) }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>CMP</label>
                <input type="text" name="cmp" class="form-control"
                       value="{{ old('cmp', $medico->cmp) }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>Especialidad</label>
                <select name="especialidad_id" class="form-select" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($especialidades as $especialidad)
                        <option value="{{ $especialidad->id }}"
                            {{ old('especialidad_id', $medico->especialidad_id) == $especialidad->id ? 'selected' : '' }}>
                            {{ $especialidad->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label>Estado</label>
                <select name="estado" class="form-select" required>
                    <option value="1" {{ old('estado', $medico->estado) == 1 ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ old('estado', $medico->estado) == 0 ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
        </div>

        <button class="btn btn-primary">💾 Actualizar Médico</button>
        <a href="{{ route('medicos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection