@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- TÍTULO -->
    <div class="mb-4">
        <h4 class="fw-bold text-primary">
            <i class="bi bi-calendar-plus"></i> Registro de Nueva Cita Médica
        </h4>
        <p class="text-muted mb-0">Sistema de Gestión Hospitalaria</p>
    </div>

    <form method="POST" action="{{ route('citas.store') }}">
        @csrf

        <!-- DATOS DEL PACIENTE -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-person-badge"></i> Datos del Paciente
            </div>
            <div class="card-body bg-light">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Paciente</label>
                        <select name="paciente_id" class="form-select" required>
                            <option value="">Seleccione un paciente</option>
                            @foreach($pacientes as $p)
                                <option value="{{ $p->id }}">{{ $p->nombres }} {{ $p->apellidos }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- DATOS MÉDICOS -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-success text-white">
                <i class="bi bi-heart-pulse"></i> Datos Médicos
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Médico</label>
                        <select name="medico_id" class="form-select" required>
                            <option value="">Seleccione un médico</option>
                            @foreach($medicos as $m)
                                <option value="{{ $m->id }}">{{ $m->nombres }} {{ $m->apellidos }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Especialidad</label>
                        <select name="especialidad_id" class="form-select" required>
                          <option value="">Seleccione una especialidad</option>
                          @foreach($especialidades as $e)
                            <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                          @endforeach
                        </select>

                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Fecha y Hora</label>
                        <input type="datetime-local" name="fecha_hora" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Turno</label>
                        <select name="turno" class="form-select">
                            <option>Mañana</option>
                            <option>Tarde</option>
                            <option>Noche</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Prioridad</label>
                        <select name="prioridad" class="form-select">
                            <option>Normal</option>
                            <option>Urgente</option>
                            <option>Emergente</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- DATOS ADMINISTRATIVOS -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-info text-white">
                <i class="bi bi-clipboard-data"></i> Datos Administrativos
            </div>
            <div class="card-body bg-light">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tipo de Cita</label>
                        <select name="tipo_cita" class="form-select">
                            <option>Consulta</option>
                            <option>Emergencia</option>
                            <option>Control</option>
                            <option>Procedimiento</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Origen</label>
                        <select name="origen" class="form-select">
                            <option>Presencial</option>
                            <option>Web</option>
                            <option>Referido</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Área / Servicio</label>
                        <select name="area_servicio" class="form-select">
                            <option>Consulta Externa</option>
                            <option>Emergencias</option>
                            <option>Hospitalización</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tipo de Paciente</label>
                        <select name="tipo_paciente" class="form-select">
                            <option>Particular</option>
                            <option>Seguro</option>
                            <option>Convenio</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Estado de la Cita</label>
                        <select name="estado" class="form-select">
                            <option>Programada</option>
                            <option>Confirmada</option>
                            <option>En espera</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- MOTIVO -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-warning">
                <i class="bi bi-chat-left-text"></i> Motivo de la Consulta
            </div>
            <div class="card-body">
                <textarea name="motivo" class="form-control" rows="3"
                    placeholder="Describa el motivo clínico de la cita"></textarea>
            </div>
        </div>

        <!-- BOTONES -->
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('citas.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Cancelar
            </a>
            <button class="btn btn-success px-4">
                <i class="bi bi-save"></i> Guardar Cita
            </button>
        </div>

    </form>
</div>
@endsection
