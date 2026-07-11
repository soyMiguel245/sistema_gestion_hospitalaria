<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log; // <-- Import correcto
use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\AtencionMedica;
use App\Models\Diagnostico;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon; // <-- Para fechas

class ReporteController extends Controller
{
    // Dashboard de reportes
    public function dashboard(Request $request)
    {
        // Fechas seguras usando Carbon
        $fecha_inicio = $request->fecha_inicio 
            ? Carbon::parse($request->fecha_inicio)->startOfDay() 
            : now()->subMonth()->startOfDay();

        $fecha_fin = $request->fecha_fin 
            ? Carbon::parse($request->fecha_fin)->endOfDay() 
            : now()->endOfDay();

        // Total de pacientes atendidos
        $pacientesAtendidos = AtencionMedica::whereBetween('created_at', [$fecha_inicio, $fecha_fin])->count();

        // Diagnósticos frecuentes (seguro con try/catch)
        try {
            $diagnosticos = Diagnostico::with('atencionMedica')
                ->whereHas('atencionMedica', function($query) use ($fecha_inicio, $fecha_fin) {
                    $query->whereBetween('created_at', [$fecha_inicio, $fecha_fin]);
                })
                ->get();
        } catch (\Exception $e) {
            Log::error('Error diagnósticos: ' . $e->getMessage()); // <-- corregido
            $diagnosticos = collect(); // colección vacía
        }

        // Ingresos por tipo de paciente
        try {
            $ingresos = AtencionMedica::selectRaw('tipo_paciente, SUM(costo - descuento) as total')
                ->whereBetween('created_at', [$fecha_inicio, $fecha_fin])
                ->groupBy('tipo_paciente')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error ingresos: ' . $e->getMessage()); // <-- corregido
            $ingresos = collect();
        }

        // Retornar la vista con los datos
        return view('reportes.dashboard', compact(
            'pacientesAtendidos',
            'diagnosticos',
            'ingresos',
            'fecha_inicio',
            'fecha_fin'
        ));
    }

    // Exportar PDF
    public function exportPDF(Request $request)
    {
        $reporte = $request->reporte ?? 'reporte_general';
        $pdf = Pdf::loadView('reportes.export_pdf', compact('reporte'));
        return $pdf->download($reporte . '_' . now()->format('Ymd') . '.pdf');
    }
}
