<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Cita;
use App\Models\HistoriaClinica;
use App\Models\AtencionMedica;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPacientes = Paciente::count();
        $totalCitas = Cita::count();
        $totalHistorias = HistoriaClinica::count();
        $totalAtenciones = AtencionMedica::count();

        return view('dashboard', compact(
            'totalPacientes',
            'totalCitas',
            'totalHistorias',
            'totalAtenciones'
        ));
    }
}
