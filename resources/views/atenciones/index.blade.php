@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- TÍTULO -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary">
            <i class="fa-solid fa-stethoscope me-2"></i>
            Gestión de Atenciones Médicas
        </h3>

        <a href="{{ route('atenciones.create') }}" class="btn btn-success">
            <i class="fa-solid fa-plus me-1"></i> Nueva Atención
        </a>
    </div>

    <!-- BUSCADOR -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body bg-light rounded">
            <form method="GET" action="{{ route('atenciones.index') }}">
                <div class="row g-2 align-items-center">
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </span>
                            <input
                                type="text"
                                name="buscar"
                                class="form-control"
                                placeholder="Buscar por paciente, médico o diagnóstico..."
                                value="{{ request('buscar') }}"
                            >
                        </div>
                    </div>
                    <div class="col-md-2 d-grid">
                        <button class="btn btn-primary">
                            <i class="fa-solid fa-filter"></i> Buscar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- TABLA -->
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white fw-bold">
            <i class="fa-solid fa-notes-medical me-2"></i>
            Registro de Atenciones
        </div>

        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color:#e9f2ff;" class="text-center fw-semibold">
                    <tr>
                        <th class="text-primary"><i class="fa-solid fa-hashtag"></i></th>
                        <th class="text-primary"><i class="fa-solid fa-user-injured me-1"></i>Paciente</th>
                        <th class="text-success"><i class="fa-solid fa-user-doctor me-1"></i>Médico</th>
                        <th class="text-info"><i class="fa-solid fa-calendar-days me-1"></i>Fecha</th>
                        <th class="text-warning"><i class="fa-solid fa-file-medical me-1"></i>Diagnóstico</th>
                        <th class="text-success"><i class="fa-solid fa-circle-check me-1"></i>Estado</th>
                        <th class="text-danger"><i class="fa-solid fa-gears me-1"></i>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($atenciones as $atencion)
                        <tr>
                            <td class="text-center fw-bold">{{ $atencion->id }}</td>

                            <td>
                                <i class="fa-solid fa-user-injured text-primary me-1"></i>
                                {{ $atencion->paciente->nombres ?? '-' }} {{ $atencion->paciente->apellidos ?? '' }}
                            </td>

                            <td>
                                <i class="fa-solid fa-user-doctor text-success me-1"></i>
                                {{ $atencion->medico->nombres ?? '-' }} {{ $atencion->medico->apellidos ?? '' }}
                            </td>

                            <td class="text-center">
                                <i class="fa-solid fa-calendar-days me-1 text-secondary"></i>
                                @if($atencion->cita)
                                    {{ \Carbon\Carbon::parse($atencion->cita->fecha_hora)->format('d/m/Y H:i') }}
                                @else
                                    {{ \Carbon\Carbon::parse($atencion->created_at)->format('d/m/Y H:i') }}
                                @endif
                            </td>

                            <td>
                                {{ Str::limit($atencion->diagnostico, 40, '...') ?? '-' }}
                            </td>

                            <td class="text-center">
                                @if($atencion->estado == 'Atendido')
                                    <span class="badge bg-success">
                                        <i class="fa-solid fa-circle-check"></i> Atendido
                                    </span>
                                @elseif($atencion->estado == 'En Progreso')
                                    <span class="badge bg-info text-white">
                                        <i class="fa-solid fa-spinner"></i> En Progreso
                                    </span>
                                @elseif($atencion->estado == 'Alta')
                                    <span class="badge bg-secondary text-white">
                                        <i class="fa-solid fa-check-double"></i> Alta
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark">
                                        <i class="fa-solid fa-clock"></i> Pendiente
                                    </span>
                                @endif
                            </td>

                            <td class="text-center">
                                <a href="{{ route('atenciones.show', $atencion->id) }}"
                                   class="btn btn-sm btn-primary"
                                   title="Ver">
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                                <a href="{{ route('atenciones.edit', $atencion->id) }}"
                                   class="btn btn-sm btn-warning"
                                   title="Editar">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                <form action="{{ route('atenciones.destroy', $atencion->id) }}"
                                      method="POST"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('¿Eliminar esta atención?')"
                                            title="Eliminar">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fa-solid fa-folder-open fa-2x mb-2"></i>
                                <div>No hay atenciones registradas</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINACIÓN -->
        <div class="card-footer bg-white">
            {{ $atenciones->links() }}
        </div>
    </div>

</div>
@endsection
