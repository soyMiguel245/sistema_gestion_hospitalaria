<?php

namespace App\Http\Controllers;

use App\Models\Medico;
use App\Models\Especialidad;
use Illuminate\Http\Request;

class MedicoController extends Controller
{
    public function index()
    {
        // Obtener todos los médicos con su especialidad
        $medicos = Medico::with('especialidad')
            ->orderBy('nombres') // usar 'nombres' que existe
            ->get();

        return view('medicos.index', compact('medicos'));
    }

    public function create()
    {
        $especialidades = Especialidad::where('estado', 1)
            ->orderBy('nombre')
            ->get();

        return view('medicos.create', compact('especialidades'));
    }

    public function store(Request $request)
    {
        // Validación de los datos
        $request->validate([
            'nombres' => 'required|string',
            'apellidos' => 'required|string',
            'dni' => 'required|string|unique:medicos,dni',
            'cmp' => 'required|string|unique:medicos,cmp',
            'especialidad_id' => 'required|exists:especialidades,id',
            'estado' => 'required|boolean',
        ]);

        // Crear médico con los datos validados
        Medico::create($request->only([
            'nombres', 
            'apellidos', 
            'dni', 
            'cmp', 
            'especialidad_id', 
            'estado'
        ]));

        return redirect()->route('medicos.index')
            ->with('success', 'Médico registrado correctamente');
    }

    public function edit(Medico $medico)
    {
        $especialidades = Especialidad::where('estado', 1)
            ->orderBy('nombre')
            ->get();

        return view('medicos.edit', compact('medico', 'especialidades'));
    }

    public function update(Request $request, Medico $medico)
    {
        $request->validate([
            'nombres' => 'required|string',
            'apellidos' => 'required|string',
            'dni' => 'required|string|unique:medicos,dni,' . $medico->id,
            'cmp' => 'required|string|unique:medicos,cmp,' . $medico->id,
            'especialidad_id' => 'required|exists:especialidades,id',
            'estado' => 'required|boolean',
        ]);

        $medico->update($request->only([
            'nombres', 
            'apellidos', 
            'dni', 
            'cmp', 
            'especialidad_id', 
            'estado'
        ]));

        return redirect()->route('medicos.index')
            ->with('success', 'Médico actualizado correctamente');
    }

    public function destroy(Medico $medico)
    {
        $medico->delete();

        return redirect()->route('medicos.index')
            ->with('success', 'Médico eliminado correctamente');
    }
}
