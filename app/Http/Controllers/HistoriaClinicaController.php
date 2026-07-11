<?php

namespace App\Http\Controllers;

use App\Models\HistoriaClinica;
use App\Models\Paciente;
use App\Models\Cita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoriaClinicaController extends Controller
{
    public function index()
    {
        $historias = HistoriaClinica::with(['paciente', 'medico'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('historias.index', compact('historias'));
    }

    public function create()
    {
        $pacientes = Paciente::all();
        $citas = Cita::all();

        return view('historias.create', compact('pacientes', 'citas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required',
            'motivo_consulta' => 'required',
        ]);

        HistoriaClinica::create([
            'paciente_id' => $request->paciente_id,
            'cita_id' => $request->cita_id,
            'medico_id' => Auth::id(),
            'registrado_por' => Auth::id(),
            'motivo_consulta' => $request->motivo_consulta,
            'antecedentes_personales' => $request->antecedentes_personales,
            'antecedentes_familiares' => $request->antecedentes_familiares,
            'enfermedad_actual' => $request->enfermedad_actual,
            'examen_fisico' => $request->examen_fisico,
            'diagnostico_principal' => $request->diagnostico_principal,
            'tratamiento' => $request->tratamiento,
            'indicaciones' => $request->indicaciones,
        ]);

        return redirect()->route('historias.index')
            ->with('success', 'Historia clínica registrada correctamente');
    }

    public function cerrar(HistoriaClinica $historia)
    {
        $historia->update([
            'estado' => 'cerrada',
            'alta_medica' => true
        ]);

        return back()->with('success', 'Historia clínica cerrada');
    }
    public function show(HistoriaClinica $historia)
    {
        $historia->load(['paciente', 'medico', 'cita']);
        return view('historias.show', compact('historia'));
    }
    


}

