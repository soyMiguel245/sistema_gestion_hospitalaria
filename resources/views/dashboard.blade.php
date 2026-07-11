@extends('layouts.app')

@section('content')

<style>
/* ===== HEADER HOSPITALARIO PROFESIONAL ===== */
.dashboard-header {
    background-color: rgb(8, 2, 70);
    color: #ffffff;
    border-radius: 5px;
    padding: 10px 10px;
    margin-bottom: 30px;
}

.dashboard-header h2 {
    font-size: 1.8rem !important;
    font-weight: 300 !important;
    margin-bottom: 2px !important;
}

.dashboard-header p {
    font-size: 1.1rem !important;
    margin: 0 !important;
    opacity: 0.85;
}

/* ===== TARJETAS ===== */
.dashboard-card {
    border: none;
    border-radius: 1px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    transition: all .25s ease;
}

.dashboard-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 24px rgba(0,0,0,0.14);
}

.icon-box {
    width: 45px;
    height: 45px;
    border-radius: 10px;
    font-size: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.bg-paciente { background: #0d6efd; color:#fff; }
.bg-cita     { background: #198754; color:#fff; }
.bg-historia { background: #ffc107; color:#000; }
.bg-atencion { background: #dc3545; color:#fff; }
</style>

<div class="container-fluid">

    <!-- ===== HEADER ===== -->
    <div class="dashboard-header">
        <h2>
            <i class="bi bi-hospital"></i> Panel de Control Hospitalario
        </h2>
        <p>
            Bienvenido, <strong>{{ auth()->user()->name }}</strong> — {{ now()->format('d/m/Y') }}
        </p>
    </div>

    <!-- ===== TARJETAS INDICADORES ===== -->
    <div class="row g-4 mb-4">

        <!-- PACIENTES -->
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted fw-semibold">Pacientes Registrados</small>
                        <div class="icon-box bg-paciente">
                            <i class="bi bi-person-fill"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-0">{{ $totalPacientes }}</h4>
                    <small class="text-muted">Pacientes activos en el sistema</small>
                    <div class="mt-2">
                        <a href="{{ route('pacientes.index') }}" class="text-primary">
                            Ver pacientes →
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- CITAS -->
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted fw-semibold">Citas Programadas</small>
                        <div class="icon-box bg-cita">
                            <i class="bi bi-calendar-check-fill"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-0">{{ $totalCitas }}</h4>
                    <small class="text-muted">Agenda médica activa</small>
                    <div class="mt-2">
                        <a href="{{ route('citas.index') }}" class="text-success">
                            Ver agenda →
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- HISTORIAS -->
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted fw-semibold">Historias Clínicas</small>
                        <div class="icon-box bg-historia">
                            <i class="bi bi-file-medical-fill"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-0">{{ $totalHistorias }}</h4>
                    <small class="text-muted">Registros clínicos activos</small>
                    <div class="mt-2">
                        <a href="{{ route('historias.index') }}" class="text-warning">
                            Ver historias →
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ATENCIONES -->
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted fw-semibold">Atenciones Médicas</small>
                        <div class="icon-box bg-atencion">
                            <i class="bi bi-heart-pulse-fill"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-0">{{ $totalAtenciones }}</h4>
                    <small class="text-muted">Atenciones registradas</small>
                    <div class="mt-2">
                        <a href="{{ route('atenciones.index') }}" class="text-danger">
                            Ver atenciones →
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ===== RESUMEN DEL DÍA ===== -->
    <div class="row mt-4">

        <div class="col-lg-6">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-calendar-day"></i> Actividad del día
                    </h6>

                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between">
                            Citas programadas hoy
                            <span class="fw-bold">{{ $totalCitas }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            Atenciones realizadas
                            <span class="fw-bold">{{ $totalAtenciones }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            Historias activas
                            <span class="fw-bold">{{ $totalHistorias }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- ===== ACCIONES RÁPIDAS ===== -->
        <div class="col-lg-6">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-lightning-charge"></i> Acciones rápidas
                    </h6>

                    <div class="d-grid gap-2">
                        <a href="{{ route('pacientes.create') }}" class="btn btn-outline-primary btn-sm">
                            ➕ Registrar paciente
                        </a>
                        <a href="{{ route('citas.create') }}" class="btn btn-outline-success btn-sm">
                            ➕ Programar cita
                        </a>
                        <a href="{{ route('atenciones.create') }}" class="btn btn-outline-danger btn-sm">
                            ➕ Nueva atención
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection
