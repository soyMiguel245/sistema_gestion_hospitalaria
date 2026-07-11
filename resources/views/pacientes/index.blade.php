@extends('layouts.app')

@section('content')

{{-- ================== COLORES SOLO PARA ICONOS ================== --}}
<style>
    .icon-primary   { color: #0d6efd; }
    .icon-success   { color: #198754; }
    .icon-info      { color: #0dcaf0; }
    .icon-warning   { color: #ffc107; }
    .icon-danger    { color: #dc3545; }
    .icon-muted     { color: #6c757d; }
</style>

<div class="container-fluid px-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-primary mb-1">
                <i class="bi bi-people-fill icon-primary me-1"></i>
                Gestión de Pacientes
            </h3>
            <p class="text-muted mb-0">
                <i class="bi bi-info-circle icon-info"></i>
                Administración clínica y control de pacientes registrados
            </p>
        </div>

        <a href="{{ route('pacientes.create') }}"
           class="btn btn-primary btn-lg shadow-sm">
            <i class="bi bi-person-plus-fill"></i> Nuevo Paciente
        </a>
    </div>

    {{-- CARD --}}
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-4">

            {{-- BUSCADOR --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-primary text-white">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="buscarPaciente"
                               class="form-control"
                               placeholder="Buscar por DNI, nombre o apellido">
                    </div>
                </div>
            </div>

            {{-- TABLA --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tablaPacientes">
                    <thead class="table-primary text-center align-middle">
                        <tr>
                            <th><i class="bi bi-hash icon-muted"></i></th>
                            <th><i class="bi bi-credit-card-2-front icon-primary"></i> DNI</th>
                            <th><i class="bi bi-person-badge-fill icon-primary"></i> Paciente</th>
                            <th><i class="bi bi-telephone-fill icon-success"></i> Contacto</th>
                            <th><i class="bi bi-shield-check icon-info"></i> Seguro</th>
                            <th><i class="bi bi-toggle-on icon-success"></i> Estado</th>
                            <th><i class="bi bi-gear-fill icon-muted"></i> Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($pacientes as $paciente)
                        <tr>
                            {{-- # --}}
                            <td class="text-center fw-semibold">
                                {{ $loop->iteration }}
                            </td>

                            {{-- DNI --}}
                            <td class="fw-semibold">
                                <i class="bi bi-credit-card icon-primary"></i>
                                {{ $paciente->dni }}
                            </td>

                            {{-- PACIENTE --}}
                            <td>
                                <div class="fw-semibold">
                                    <i class="bi bi-person-fill icon-primary"></i>
                                    {{ $paciente->nombres }} {{ $paciente->apellidos }}
                                </div>
                                <small class="text-muted">
                                    <i class="bi bi-gender-ambiguous icon-warning"></i>
                                    {{ $paciente->sexo }}
                                    |
                                    <i class="bi bi-calendar-event icon-info"></i>
                                    {{ $paciente->fecha_nacimiento }}
                                </small>
                            </td>

                            {{-- CONTACTO --}}
                            <td>
                                <div>
                                    <i class="bi bi-telephone icon-success"></i>
                                    {{ $paciente->telefono ?? '—' }}
                                </div>
                                <small class="text-muted">
                                    <i class="bi bi-envelope icon-info"></i>
                                    {{ $paciente->correo ?? '—' }}
                                </small>
                            </td>

                            {{-- SEGURO --}}
                            <td class="text-center">
                                <span class="badge bg-info px-3 py-2">
                                    <i class="bi bi-shield-lock-fill"></i>
                                    {{ $paciente->tipo_seguro ?? 'No definido' }}
                                </span>
                            </td>

                            {{-- ESTADO --}}
                            <td class="text-center">
                                @if($paciente->estado === 'Activo')
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="bi bi-check-circle-fill"></i> Activo
                                    </span>
                                @else
                                    <span class="badge bg-secondary px-3 py-2">
                                        <i class="bi bi-x-circle-fill"></i> Inactivo
                                    </span>
                                @endif
                            </td>

                            {{-- ACCIONES --}}
                            <td class="text-center">
                                <a href="{{ route('pacientes.edit', $paciente) }}"
                                   class="btn btn-sm btn-outline-primary me-1"
                                   title="Editar Paciente">
                                    <i class="bi bi-pencil-square icon-primary"></i>
                                </a>

                                <a href="{{ route('pacientes.show', $paciente) }}"
                                   class="btn btn-sm btn-outline-info me-1"
                                   title="Ver Ficha Clínica">
                                    <i class="bi bi-eye-fill icon-info"></i>
                                </a>

                                <form action="{{ route('pacientes.destroy', $paciente) }}"
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('¿Eliminar este paciente?')"
                                            title="Eliminar Paciente">
                                        <i class="bi bi-trash-fill icon-danger"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-info-circle-fill icon-muted"></i>
                                No hay pacientes registrados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

{{-- BUSCADOR --}}
<script>
document.getElementById('buscarPaciente').addEventListener('keyup', function () {
    const filtro = this.value.toLowerCase();
    document.querySelectorAll('#tablaPacientes tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(filtro) ? '' : 'none';
    });
});
</script>
@endsection
