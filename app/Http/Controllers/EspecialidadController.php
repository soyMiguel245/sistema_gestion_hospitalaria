<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use Illuminate\Http\Request;

class EspecialidadController extends Controller
{
    public function index()
    {
        $especialidades = Especialidad::orderBy('nombre')->get();
        return view('especialidades.index', compact('especialidades'));
    }

    public function create()
    {
        return view('especialidades.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|unique:especialidades,nombre',
            'descripcion' => 'nullable|string',
            'estado' => 'required|boolean'
        ]);

        Especialidad::create($request->all());

        return redirect()->route('especialidades.index')
            ->with('success', 'Especialidad registrada correctamente');
    }

    public function edit(Especialidad $especialidad)
    {
        return view('especialidades.edit', compact('especialidad'));
    }

    public function update(Request $request, Especialidad $especialidad)
    {
        $request->validate([
            'nombre' => 'required|unique:especialidades,nombre,' . $especialidad->id,
            'descripcion' => 'nullable|string',
            'estado' => 'required|boolean'
        ]);

        $especialidad->update($request->all());

        return redirect()->route('especialidades.index')
            ->with('success', 'Especialidad actualizada correctamente');
    }

    public function destroy(Especialidad $especialidad)
    {
        $especialidad->delete();

        return redirect()->route('especialidades.index')
            ->with('success', 'Especialidad eliminada correctamente');
    }
}
