

@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary">
            <i class="bi bi-shield-lock-fill"></i> Bitácora de Auditoría
        </h3>
    </div>

    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body bg-light rounded">
            <form method="GET" action="{{ route('bitacora.index') }}">
                <div class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <select name="modulo" class="form-select" onchange="this.form.submit()">
                            <option value="">Todos los módulos</option>
                            <option value="paciente" {{ request('modulo') == 'paciente' ? 'selected' : '' }}>Pacientes</option>
                            <option value="cita" {{ request('modulo') == 'cita' ? 'selected' : '' }}>Citas</option>
                            <option value="atencion_medica" {{ request('modulo') == 'atencion_medica' ? 'selected' : '' }}>Atenciones Médicas</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white fw-bold">
            <i class="bi bi-clock-history"></i> Registro de Actividad
        </div>

        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color:#e9f2ff;" class="text-center fw-semibold">
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Módulo</th>
                        <th>Acción</th>
                        <th>Registro</th>
                        <th>Cambios</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registros as $r)
                    <tr>
                        <td class="text-center">{{ $r->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $r->causer->name ?? 'Sistema' }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ $r->log_name }}</span>
                        </td>
                        <td class="text-center">
                            @if($r->description == 'created')
                                <span class="badge bg-success">Creado</span>
                            @elseif($r->description == 'updated')
                                <span class="badge bg-warning text-dark">Editado</span>
                            @elseif($r->description == 'deleted')
                                <span class="badge bg-danger">Eliminado</span>
                            @else
                                {{ $r->description }}
                            @endif
                        </td>
                        <td class="text-center">#{{ $r->subject_id }}</td>
                        <td>
                            @if($r->properties->has('attributes'))
                                <small class="text-muted">
                                    {{ collect($r->properties['attributes'])->keys()->implode(', ') }}
                                </small>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No hay registros de actividad todavía
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white">
            {{ $registros->links() }}
        </div>
    </div>

</div>
@endsection