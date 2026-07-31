<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte PDF - {{ $reporte }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
        .footer { text-align: center; font-size: 10px; margin-top: 30px; }
        .resumen { display: table; width: 100%; margin-bottom: 20px; }
        .resumen-item {
            display: table-cell;
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
            width: 33%;
        }
        .resumen-item .numero { font-size: 20px; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Reporte: {{ ucfirst($reporte) }}</h1>
    <p>
        Fecha de generación: {{ date('d/m/Y H:i') }}
        @if(isset($fecha_inicio) && isset($fecha_fin))
            &nbsp;|&nbsp; Rango: {{ \Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') }}
            al {{ \Carbon\Carbon::parse($fecha_fin)->format('d/m/Y') }}
        @endif
    </p>

    @if($reporte === 'dashboard')
        {{-- 👇 NUEVO: antes no existía esta rama, por eso el PDF salía
             vacío cuando se exportaba desde el botón del dashboard. --}}
        <div class="resumen">
            <div class="resumen-item">
                <div class="numero">{{ $pacientesAtendidos ?? 0 }}</div>
                <div>Atenciones en el periodo</div>
            </div>
            <div class="resumen-item">
                <div class="numero">{{ $diagnosticos->count() ?? 0 }}</div>
                <div>Diagnósticos registrados</div>
            </div>
            <div class="resumen-item">
                <div class="numero">S/ {{ number_format($ingresos->sum('total') ?? 0, 2) }}</div>
                <div>Ingresos totales</div>
            </div>
        </div>

        <h3>Ingresos por tipo de paciente</h3>
        <table>
            <thead>
                <tr>
                    <th>Tipo de paciente</th>
                    <th>Total (S/)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ingresos as $ingreso)
                <tr>
                    <td>{{ $ingreso->tipo_paciente }}</td>
                    <td>{{ number_format($ingreso->total, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="2">Sin datos de ingresos en este periodo.</td></tr>
                @endforelse
            </tbody>
        </table>

        <h3>Diagnósticos del periodo</h3>
        <table>
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>Descripción</th>
                    <th>Tipo</th>
                    <th>CIE-10</th>
                </tr>
            </thead>
            <tbody>
                @forelse($diagnosticos as $diagnostico)
                <tr>
                    <td>{{ $diagnostico->atencionMedica->paciente->nombres ?? 'N/A' }}
                        {{ $diagnostico->atencionMedica->paciente->apellidos ?? '' }}</td>
                    <td>{{ $diagnostico->descripcion }}</td>
                    <td>{{ $diagnostico->tipo }}</td>
                    <td>{{ $diagnostico->cie10 ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="4">Sin diagnósticos registrados en este periodo.</td></tr>
                @endforelse
            </tbody>
        </table>

    @elseif($reporte === 'atenciones')
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Paciente</th>
                    <th>Médico</th>
                    <th>Diagnóstico</th>
                    <th>Procedimientos</th>
                    <th>Tratamiento</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach(\App\Models\AtencionMedica::with(['paciente', 'medico'])->get() as $atencion)
                <tr>
                    <td>{{ $atencion->id }}</td>
                    <td>{{ $atencion->paciente->nombres ?? 'N/A' }} {{ $atencion->paciente->apellidos ?? '' }}</td>
                    <td>{{ $atencion->medico->name ?? 'N/A' }}</td>
                    <td>{{ $atencion->diagnostico }}</td>
                    <td>{{ $atencion->procedimientos }}</td>
                    <td>{{ $atencion->tratamiento }}</td>
                    <td>{{ $atencion->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($reporte === 'pacientes')
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>DNI</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Estado</th>
                    <th>Fecha de registro</th>
                </tr>
            </thead>
            <tbody>
                @foreach(\App\Models\Paciente::all() as $paciente)
                <tr>
                    <td>{{ $paciente->id }}</td>
                    <td>{{ $paciente->dni }}</td>
                    <td>{{ $paciente->nombres }}</td>
                    <td>{{ $paciente->apellidos }}</td>
                    <td>{{ $paciente->estado }}</td>
                    <td>{{ $paciente->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No hay datos para mostrar.</p>
    @endif

    <div class="footer">
        Sistema de Gestión Hospitalaria - {{ date('Y') }}
    </div>
</body>
</html>