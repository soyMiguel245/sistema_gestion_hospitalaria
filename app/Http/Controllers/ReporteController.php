<?php

namespace App\Http\Controllers;

use App\Models\AtencionMedica;
use App\Models\Diagnostico;
use App\Models\Paciente;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReporteController extends Controller
{
    /**
     * 👇 NUEVO: los reportes mezclan datos clínicos (diagnósticos) y
     * financieros (ingresos), así que se restringen a administrador y
     * médico. Recepción no debe verlos (mismo criterio que HistorialClinico).
     */
    public function __construct()
    {
        $this->middleware(function (Request $request, $next) {
            if (! $request->user() || ! $request->user()->hasRole(['administrador', 'medico'])) {
                abort(403, 'No tienes permiso para ver reportes.');
            }

            return $next($request);
        });
    }

    /**
     * Helper común: parsea el rango de fechas de la petición, con el
     * mismo fallback que ya usaba dashboard() (último mes por defecto).
     */
    private function rangoFechas(Request $request): array
    {
        $inicio = $request->fecha_inicio
            ? Carbon::parse($request->fecha_inicio)->startOfDay()
            : now()->subMonth()->startOfDay();

        $fin = $request->fecha_fin
            ? Carbon::parse($request->fecha_fin)->endOfDay()
            : now()->endOfDay();

        return [$inicio, $fin];
    }

    // Dashboard de reportes
    public function dashboard(Request $request)
    {
        [$fecha_inicio, $fecha_fin] = $this->rangoFechas($request);

        $pacientesAtendidos = AtencionMedica::whereBetween('created_at', [$fecha_inicio, $fecha_fin])->count();

        try {
            $diagnosticos = Diagnostico::with('atencionMedica')
                ->whereHas('atencionMedica', function ($query) use ($fecha_inicio, $fecha_fin) {
                    $query->whereBetween('created_at', [$fecha_inicio, $fecha_fin]);
                })
                ->get();
        } catch (\Exception $e) {
            Log::error('Error diagnósticos: '.$e->getMessage());
            $diagnosticos = collect();
        }

        try {
            $ingresos = AtencionMedica::selectRaw('tipo_paciente, SUM(costo - descuento) as total')
                ->whereBetween('created_at', [$fecha_inicio, $fecha_fin])
                ->groupBy('tipo_paciente')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error ingresos: '.$e->getMessage());
            $ingresos = collect();
        }

        return view('reportes.dashboard', compact(
            'pacientesAtendidos',
            'diagnosticos',
            'ingresos',
            'fecha_inicio',
            'fecha_fin'
        ));
    }

    /**
     * 👇 NUEVO: reporte de pacientes — totales y desgloses por sexo,
     * tipo de seguro y estado, además del listado del periodo.
     */
    public function pacientes(Request $request)
    {
        [$fecha_inicio, $fecha_fin] = $this->rangoFechas($request);

        $pacientes = Paciente::whereBetween('fecha_registro', [$fecha_inicio, $fecha_fin])
            ->orderByDesc('fecha_registro')
            ->get();

        $totalPacientes = $pacientes->count();
        $porSexo = $pacientes->groupBy('sexo')->map->count();
        $porTipoSeguro = $pacientes->groupBy('tipo_seguro')->map->count();
        $porEstado = $pacientes->groupBy('estado')->map->count();

        return view('reportes.pacientes', compact(
            'pacientes',
            'totalPacientes',
            'porSexo',
            'porTipoSeguro',
            'porEstado',
            'fecha_inicio',
            'fecha_fin'
        ));
    }

    /**
     * 👇 NUEVO: historial de atenciones médicas del periodo, con
     * paciente, médico y estado — es el "libro" de todo lo atendido.
     */
    public function historial(Request $request)
    {
        [$fecha_inicio, $fecha_fin] = $this->rangoFechas($request);

        $atenciones = AtencionMedica::with(['paciente', 'medico'])
            ->whereBetween('created_at', [$fecha_inicio, $fecha_fin])
            ->orderByDesc('created_at')
            ->get();

        $porEstado = $atenciones->groupBy('estado')->map->count();

        return view('reportes.historial', compact(
            'atenciones',
            'porEstado',
            'fecha_inicio',
            'fecha_fin'
        ));
    }

    /**
     * 👇 NUEVO: diagnósticos más frecuentes del periodo.
     * CORREGIDO respecto al dashboard: se agrupa por 'descripcion' + 'cie10',
     * ya que Diagnostico no tiene ningún campo llamado 'nombre'.
     */
    public function diagnosticos(Request $request)
    {
        [$fecha_inicio, $fecha_fin] = $this->rangoFechas($request);

        $diagnosticos = Diagnostico::whereHas('atencionMedica', function ($query) use ($fecha_inicio, $fecha_fin) {
            $query->whereBetween('created_at', [$fecha_inicio, $fecha_fin]);
        })->get();

        $frecuencia = $diagnosticos
            ->groupBy(fn ($d) => $d->descripcion.($d->cie10 ? " ({$d->cie10})" : ''))
            ->map->count()
            ->sortDesc();

        return view('reportes.diagnosticos', compact(
            'frecuencia',
            'fecha_inicio',
            'fecha_fin'
        ));
    }

    /**
     * 👇 NUEVO: procedimientos realizados en el periodo. Como el campo
     * es texto libre (no normalizado en una tabla propia), se listan las
     * atenciones que sí registraron procedimientos, en vez de agruparlos
     * (agrupar texto libre daría resultados poco útiles).
     */
    public function procedimientos(Request $request)
    {
        [$fecha_inicio, $fecha_fin] = $this->rangoFechas($request);      

  $atenciones = AtencionMedica::with(['paciente', 'medico'])
            ->whereBetween('created_at', [$fecha_inicio, $fecha_fin])
            ->whereNotNull('procedimientos')
            ->orderByDesc('created_at')
            ->get()
            // 👇 CORREGIDO: 'procedimientos' está cifrado (AES-256), por lo
            // que el filtro de "no vacío" ya no puede aplicarse en SQL —
            // el valor crudo en la BD nunca es '', aunque el contenido
            // descifrado sí lo sea. Se filtra en PHP, después del cast.
            ->filter(fn ($a) => filled($a->procedimientos))
            ->values();

        return view('reportes.procedimientos', compact(
            'atenciones',
            'fecha_inicio',
            'fecha_fin'
        ));
    }

    /**
     * 👇 NUEVO: signos vitales registrados en el periodo, con promedios
     * generales — útil para ver tendencias rápidas.
     */
    public function signos(Request $request)
    {
        [$fecha_inicio, $fecha_fin] = $this->rangoFechas($request);

        $atenciones = AtencionMedica::with(['paciente', 'medico'])
            ->whereBetween('created_at', [$fecha_inicio, $fecha_fin])
            ->orderByDesc('created_at')
            ->get();

        $promedios = [
            'frecuencia_cardiaca' => round($atenciones->avg('frecuencia_cardiaca'), 1),
            'frecuencia_respiratoria' => round($atenciones->avg('frecuencia_respiratoria'), 1),
            'temperatura' => round($atenciones->avg('temperatura'), 1),
            'saturacion_o2' => round($atenciones->avg('saturacion_o2'), 1),
        ];

        return view('reportes.signos', compact(
            'atenciones',
            'promedios',
            'fecha_inicio',
            'fecha_fin'
        ));
    }

    /**
     * 👇 NUEVO: exporta a CSV el reporte solicitado. $tipo viene de la URL
     * (reportes/export/{tipo}), se valida contra una lista blanca fija
     * para no aceptar nombres de archivo arbitrarios del usuario.
     */
    public function export(Request $request, string $tipo)
    {
        $tiposValidos = ['pacientes', 'historial', 'diagnosticos', 'procedimientos', 'signos'];

        if (! in_array($tipo, $tiposValidos)) {
            abort(404, 'Tipo de reporte no válido.');
        }

        [$fecha_inicio, $fecha_fin] = $this->rangoFechas($request);
        $filename = "reporte_{$tipo}_".now()->format('Ymd_His').'.csv';

        $callback = function () use ($tipo, $fecha_inicio, $fecha_fin) {
            $file = fopen('php://output', 'w');

            match ($tipo) {
                'pacientes' => $this->exportarPacientesCsv($file, $fecha_inicio, $fecha_fin),
                'historial' => $this->exportarHistorialCsv($file, $fecha_inicio, $fecha_fin),
                'diagnosticos' => $this->exportarDiagnosticosCsv($file, $fecha_inicio, $fecha_fin),
                'procedimientos' => $this->exportarProcedimientosCsv($file, $fecha_inicio, $fecha_fin),
                'signos' => $this->exportarSignosCsv($file, $fecha_inicio, $fecha_fin),
            };

            fclose($file);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function exportarPacientesCsv($file, $fecha_inicio, $fecha_fin): void
    {
        fputcsv($file, ['DNI', 'Nombres', 'Apellidos', 'Sexo', 'Tipo Seguro', 'Estado', 'Fecha Registro']);

        Paciente::whereBetween('fecha_registro', [$fecha_inicio, $fecha_fin])
            ->orderByDesc('fecha_registro')
            ->each(function ($p) use ($file) {
                fputcsv($file, [$p->dni, $p->nombres, $p->apellidos, $p->sexo, $p->tipo_seguro, $p->estado, $p->fecha_registro]);
            });
    }

    private function exportarHistorialCsv($file, $fecha_inicio, $fecha_fin): void
    {
        fputcsv($file, ['Fecha', 'Paciente', 'Médico', 'Motivo', 'Estado']);

        AtencionMedica::with(['paciente', 'medico'])
            ->whereBetween('created_at', [$fecha_inicio, $fecha_fin])
            ->orderByDesc('created_at')
            ->each(function ($a) use ($file) {
                fputcsv($file, [
                    $a->created_at->format('d/m/Y H:i'),
                    trim(($a->paciente->nombres ?? '-').' '.($a->paciente->apellidos ?? '')),
                    trim(($a->medico->nombres ?? '-').' '.($a->medico->apellidos ?? '')),
                    $a->motivo_consulta,
                    $a->estado,
                ]);
            });
    }

    private function exportarDiagnosticosCsv($file, $fecha_inicio, $fecha_fin): void
    {
        fputcsv($file, ['Diagnóstico', 'CIE-10', 'Cantidad']);

        $diagnosticos = Diagnostico::whereHas('atencionMedica', function ($query) use ($fecha_inicio, $fecha_fin) {
            $query->whereBetween('created_at', [$fecha_inicio, $fecha_fin]);
        })->get();

        $diagnosticos->groupBy('descripcion')->each(function ($grupo, $descripcion) use ($file) {
            fputcsv($file, [$descripcion, $grupo->first()->cie10 ?? '-', $grupo->count()]);
        });
    }

    private function exportarProcedimientosCsv($file, $fecha_inicio, $fecha_fin): void
    {
        fputcsv($file, ['Fecha', 'Paciente', 'Médico', 'Procedimientos']);

                // 👇 CORREGIDO: mismo caso que en procedimientos() — el filtro de
        // "no vacío" no puede aplicarse en SQL sobre una columna cifrada.
        AtencionMedica::with(['paciente', 'medico'])
            ->whereBetween('created_at', [$fecha_inicio, $fecha_fin])
            ->whereNotNull('procedimientos')
            ->get()
            ->filter(fn ($a) => filled($a->procedimientos))
            ->each(function ($a) use ($file)  {
                fputcsv($file, [
                    $a->created_at->format('d/m/Y H:i'),
                    trim(($a->paciente->nombres ?? '-').' '.($a->paciente->apellidos ?? '')),
                    trim(($a->medico->nombres ?? '-').' '.($a->medico->apellidos ?? '')),
                    $a->procedimientos,
                ]);
            });
    }

    private function exportarSignosCsv($file, $fecha_inicio, $fecha_fin): void
    {
        fputcsv($file, ['Fecha', 'Paciente', 'PA', 'FC', 'FR', 'Temp', 'SpO2']);

        AtencionMedica::with('paciente')
            ->whereBetween('created_at', [$fecha_inicio, $fecha_fin])
            ->each(function ($a) use ($file) {
                fputcsv($file, [
                    $a->created_at->format('d/m/Y H:i'),
                    trim(($a->paciente->nombres ?? '-').' '.($a->paciente->apellidos ?? '')),
                    $a->presion_arterial,
                    $a->frecuencia_cardiaca,
                    $a->frecuencia_respiratoria,
                    $a->temperatura,
                    $a->saturacion_o2,
                ]);
            });
    }

    // Exportar PDF (dashboard)
    /**
     * 👇 CORREGIDO: antes solo pasaba el nombre del reporte a la vista,
     * sin ningún dato — por eso el PDF se descargaba pero salía vacío.
     * Ahora calcula la misma información que ve el dashboard en pantalla
     * y se la pasa al PDF.
     */
    public function exportPDF(Request $request)
    {
        $reporte = $request->reporte ?? 'dashboard';

        [$fecha_inicio, $fecha_fin] = $this->rangoFechas($request);

        $pacientesAtendidos = AtencionMedica::whereBetween('created_at', [$fecha_inicio, $fecha_fin])->count();

        try {
            $diagnosticos = Diagnostico::with('atencionMedica.paciente')
                ->whereHas('atencionMedica', function ($query) use ($fecha_inicio, $fecha_fin) {
                    $query->whereBetween('created_at', [$fecha_inicio, $fecha_fin]);
                })
                ->get();
        } catch (\Exception $e) {
            Log::error('Error diagnósticos (PDF): '.$e->getMessage());
            $diagnosticos = collect();
        }

        try {
            $ingresos = AtencionMedica::selectRaw('tipo_paciente, SUM(costo - descuento) as total')
                ->whereBetween('created_at', [$fecha_inicio, $fecha_fin])
                ->groupBy('tipo_paciente')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error ingresos (PDF): '.$e->getMessage());
            $ingresos = collect();
        }

        $pdf = Pdf::loadView('reportes.export_pdf', compact(
            'reporte',
            'pacientesAtendidos',
            'diagnosticos',
            'ingresos',
            'fecha_inicio',
            'fecha_fin'
        ));

        return $pdf->download($reporte.'_'.now()->format('Ymd').'.pdf');
    }
}
