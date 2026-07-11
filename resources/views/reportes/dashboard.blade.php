@extends('layouts.app')

@section('content')

<style>
/* ===== ESTILO PROFESIONAL REPORTES ===== */
.reporte-header {
    background-color: #0b2e4f;
    color: #fff;
    padding: 14px 20px;
    border-radius: 6px;
    margin-bottom: 25px;
}

.reporte-header h4 {
    font-size: 1.3rem;
    font-weight: 500;
    margin: 0;
}

.reporte-header p {
    font-size: 0.85rem;
    opacity: 0.85;
    margin: 0;
}

.reporte-card {
    border: none;
    border-radius: 6px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.08);
}

.reporte-card h6 {
    font-size: 0.9rem;
    font-weight: 600;
}

.reporte-card .valor {
    font-size: 1.6rem;
    font-weight: 700;
}

.table thead {
    background-color: #f1f5f9;
}

.badge-clinico {
    background-color: #0b2e4f;
}

.btn-pdf {
    background-color: #8b0000;
    color: #fff;
}
</style>

<div class="container-fluid">

    <!-- ===== HEADER ===== -->
    <div class="reporte-header d-flex justify-content-between align-items-center">
        <div>
            <h4><i class="bi bi-clipboard-data"></i> Reportes Hospitalarios</h4>
            <p>Análisis clínico y financiero del sistema</p>
        </div>
        <div class="small">
            {{ now()->format('d/m/Y') }}
        </div>
    </div>

    <!-- ===== FILTRO FECHAS ===== -->
    <form action="{{ route('reportes.tablero') }}" method="GET" class="card reporte-card mb-4">
        <div class="card-body row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Fecha inicio</label>
                <input type="date" name="fecha_inicio" class="form-control"
                    value="{{ request('fecha_inicio', $fecha_inicio->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Fecha fin</label>
                <input type="date" name="fecha_fin" class="form-control"
                    value="{{ request('fecha_fin', $fecha_fin->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary w-100">
                    <i class="bi bi-funnel"></i> Filtrar reportes
                </button>
            </div>
        </div>
    </form>

    <!-- ===== RESUMEN CLÍNICO ===== -->
    <div class="row g-4 mb-4">

        <div class="col-lg-4">
            <div class="card reporte-card">
                <div class="card-body">
                    <h6 class="text-muted">Pacientes atendidos</h6>
                    <div class="valor">{{ $pacientesAtendidos }}</div>
                    <span class="badge badge-clinico">Periodo seleccionado</span>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card reporte-card">
                <div class="card-body">
                    <h6 class="text-muted">Ingresos totales</h6>
                    <div class="valor">
                        S/. {{ number_format($ingresos->sum('total') ?? 0, 2) }}
                    </div>
                    <span class="badge bg-success">Facturación</span>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card reporte-card">
                <div class="card-body">
                    <h6 class="text-muted">Tipos de paciente</h6>
                    @foreach($ingresos as $ingreso)
                        <div class="small d-flex justify-content-between">
                            <span>{{ ucfirst($ingreso->tipo_paciente) }}</span>
                            <strong>S/. {{ number_format($ingreso->total,2) }}</strong>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    <!-- ===== DIAGNÓSTICOS ===== -->
    <div class="card reporte-card mb-4">
        <div class="card-header bg-light fw-semibold">
            <i class="bi bi-heart-pulse"></i> Diagnósticos frecuentes
        </div>
        <div class="card-body p-0">
            @if($diagnosticos->count())
                @php
                    $diagCounts = $diagnosticos->groupBy('nombre')->map->count();
                @endphp
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Diagnóstico</th>
                            <th class="text-center">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($diagCounts as $nombre => $cantidad)
                            <tr>
                                <td>{{ $nombre }}</td>
                                <td class="text-center fw-bold">{{ $cantidad }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="p-3 text-muted">No hay diagnósticos en este periodo.</p>
            @endif
        </div>
    </div>

    <!-- ===== EXPORTAR ===== -->
    <div class="text-end">
        <a href="{{ route('reportes.exportPDF', ['reporte' => 'dashboard']) }}"
           class="btn btn-pdf">
            <i class="bi bi-file-earmark-pdf"></i> Exportar reporte PDF
        </a>
    </div>

</div>

@endsection
