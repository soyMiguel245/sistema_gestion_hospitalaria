@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- ENCABEZADO -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-primary mb-1">
                <i class="bi bi-calendar2-week-fill text-primary"></i>
                Agenda Médica
            </h4>
            <p class="text-muted mb-0">
                <i class="bi bi-info-circle-fill text-secondary"></i>
                Citas programadas
                @if(request('fecha'))
                    para el
                    <strong>{{ \Carbon\Carbon::parse(request('fecha'))->format('d/m/Y') }}</strong>
                @endif
            </p>
        </div>

        <a href="{{ route('citas.create') }}" class="btn btn-success shadow-sm">
            <i class="bi bi-plus-circle-fill text-white"></i>
            Nueva Cita
        </a>
    </div>

    <!-- FILTROS -->
    <form method="GET" class="row g-3 mb-4">

        <div class="col-md-4">
            <label class="form-label fw-semibold">
                <i class="bi bi-person-badge-fill text-success"></i>
                Médico
            </label>
            <select name="medico_id" class="form-select shadow-sm">
                <option value="">— Todos —</option>
                @foreach($medicos as $m)
                    <option value="{{ $m->id }}" {{ request('medico_id') == $m->id ? 'selected' : '' }}>
                        {{ $m->nombres }} {{ $m->apellidos }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">
                <i class="bi bi-calendar-event-fill text-primary"></i>
                Fecha
            </label>
            <input type="date"
                   name="fecha"
                   class="form-control shadow-sm"
                   value="{{ request('fecha') }}">
        </div>

        <div class="col-md-3 align-self-end">
            <button class="btn btn-primary w-100 shadow-sm">
                <i class="bi bi-search text-white"></i>
                Filtrar Agenda
            </button>
        </div>

    </form>

    <!-- TABLA -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">

            <table class="table table-hover align-middle mb-0">
                <thead style="background:#f8f9fa;">
                    <tr class="fw-semibold">
                        <th class="text-center">
                            <i class="bi bi-clock-fill text-secondary"></i>
                            Hora
                        </th>
                        <th>
                            <i class="bi bi-person-fill text-primary"></i>
                            Paciente
                        </th>
                        <th>
                            <i class="bi bi-person-badge-fill text-success"></i>
                            Médico
                        </th>
                        <th>
                            <i class="bi bi-heart-pulse-fill text-danger"></i>
                            Especialidad
                        </th>
                        <th>
                            <i class="bi bi-activity text-warning"></i>
                            Estado
                        </th>
                        <th class="text-center">
                            <i class="bi bi-gear-fill text-dark"></i>
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody>
                @forelse($citas as $cita)

                    @php
                        $estadoColor = match($cita->estado) {
                            'Programada' => 'primary',
                            'Confirmada' => 'success',
                            'En espera' => 'warning',
                            'En atención' => 'info',
                            'Atendida' => 'secondary',
                            'Cancelada' => 'danger',
                            'No asistió' => 'dark',
                            default => 'light'
                        };
                    @endphp

                    <tr>

                        <!-- HORA -->
                        <td class="text-center">
                            <span class="badge bg-light text-dark fs-6">
                                <i class="bi bi-clock-fill text-secondary"></i>
                                {{ \Carbon\Carbon::parse($cita->fecha_hora)->format('H:i') }}
                            </span>
                        </td>

                        <!-- PACIENTE -->
                        <td class="fw-semibold text-primary">
                            <i class="bi bi-person-fill text-primary"></i>
                            {{ $cita->paciente->nombres }} {{ $cita->paciente->apellidos }}
                        </td>

                        <!-- MÉDICO -->
                        <td>
                            <i class="bi bi-person-badge-fill text-success"></i>
                            {{ $cita->medico->nombres ?? '' }} {{ $cita->medico->apellidos ?? '' }}
                        </td>

                        <!-- ESPECIALIDAD -->
                        <td class="text-secondary">
                            <i class="bi bi-heart-pulse-fill text-danger"></i>
                            {{ $cita->especialidad->nombre ?? 'Sin Especialidad' }}
                        </td>

                        <!-- ESTADO -->
                        <td>
                            <span class="badge bg-{{ $estadoColor }}">
                                <i class="bi bi-circle-fill me-1"></i>
                                {{ $cita->estado }}
                            </span>
                        </td>

                        <!-- ACCIONES -->
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('citas.show',$cita) }}"
                                   class="btn btn-outline-info"
                                   title="Ver">
                                    <i class="bi bi-eye-fill text-info"></i>
                                </a>
                                <a href="{{ route('citas.edit',$cita) }}"
                                   class="btn btn-outline-warning"
                                   title="Editar">
                                    <i class="bi bi-pencil-fill text-warning"></i>
                                </a>
                            </div>
                        </td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-calendar-x-fill text-danger fs-3"></i>
                            <br>
                            No hay citas registradas
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>

        </div>
    </div>

</div>
@endsection
