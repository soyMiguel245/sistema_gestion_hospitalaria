@extends('layouts.app')

@section('content')
<div class="container">
<h3>Editar Cita</h3>

<form method="POST" action="{{ route('citas.update',$cita) }}">
@csrf
@method('PUT')

<div class="mb-3">
    <label class="form-label">Paciente</label>
    <select name="paciente_id" class="form-control">
        @foreach($pacientes as $p)
            <option value="{{ $p->id }}" {{ $cita->paciente_id==$p->id?'selected':'' }}>
                {{ $p->nombres }} {{ $p->apellidos }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Médico</label>
    <select name="medico_id" class="form-control">
        @foreach($medicos as $m)
            <option value="{{ $m->id }}" {{ $cita->medico_id==$m->id?'selected':'' }}>
                {{ $m->nombres }} {{ $m->apellidos }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Especialidad</label>
    <select name="especialidad_id" class="form-control">
        @foreach($especialidades as $e)
            <option value="{{ $e->id }}" {{ $cita->especialidad_id==$e->id?'selected':'' }}>
                {{ $e->nombre }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Fecha y Hora</label>
    <input type="datetime-local" name="fecha_hora"
        value="{{ date('Y-m-d\TH:i', strtotime($cita->fecha_hora)) }}"
        class="form-control mb-2">
</div>

<div class="mb-3">
    <label class="form-label">Estado</label>
    <select name="estado" class="form-control mb-3">
        @foreach(['Programada','Confirmada','En espera','En atención','Atendida','Cancelada','Reprogramada','No asistió'] as $e)
            <option {{ $cita->estado==$e?'selected':'' }}>{{ $e }}</option>
        @endforeach
    </select>
</div>

<div class="d-flex justify-content-end">
    <button class="btn btn-primary">Actualizar</button>
</div>
</form>
</div>
@endsection
