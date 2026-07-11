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
    </style>
</head>
<body>
    <h1>Reporte: {{ ucfirst($reporte) }}</h1>
    <p>Fecha de generación: {{ date('d/m/Y H:i') }}</p>

    @if($reporte === 'atenciones')
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
                @foreach(\App\Models\AtencionMedica::all() as $atencion)
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
