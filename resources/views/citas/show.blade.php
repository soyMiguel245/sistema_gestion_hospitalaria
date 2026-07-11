@extends('layouts.app')

@section('content')
<div class="container">
<h3>Detalle de Cita</h3>

<ul class="list-group">
<li class="list-group-item"><b>Código:</b> {{ $cita->codigo_cita }}</li>
<li class="list-group-item"><b>Paciente:</b> {{ $cita->paciente->nombres }}</li>
<li class="list-group-item"><b>Médico:</b> {{ $cita->medico->name }}</li>
<li class="list-group-item"><b>Fecha:</b> {{ $cita->fecha_hora }}</li>
<li class="list-group-item"><b>Estado:</b> {{ $cita->estado }}</li>
</ul>

<a href="{{ route('citas.index') }}" class="btn btn-secondary mt-3">Volver</a>
</div>
@endsection
