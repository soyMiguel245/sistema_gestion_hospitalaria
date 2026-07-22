<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Cita;
use App\Models\AtencionMedica;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPacientes = Paciente::count();
        $totalCitas = Cita::count();

        // 👇 CORREGIDO: HistoriaClinica ya no existe como tabla propia.
        // El "total de historias" ahora es equivalente al total de pacientes
        // que tienen al menos una atención médica registrada (su expediente
        // ya está "abierto"), en vez de contar filas de una tabla duplicada.
        $totalHistorias = Paciente::has('atencionesMedicas')->count();

        $totalAtenciones = AtencionMedica::count();

        return view('dashboard', compact(
            'totalPacientes',
            'totalCitas',
            'totalHistorias',
            'totalAtenciones'
        ));
    }
}