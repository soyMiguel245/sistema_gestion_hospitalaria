<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class EspecialidadController extends Controller
{
    /**
     * 👇 NUEVO: antes este controlador no tenía NINGÚN control de acceso.
     */
    public function __construct()
    {
        $this->authorizeResource(Especialidad::class, 'especialidad');
    }

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
        $validated = $request->validate([
            'nombre' => 'required|unique:especialidades,nombre',
            'descripcion' => 'nullable|string',
            'estado' => 'required|boolean'
        ]);

        Especialidad::create($validated);

        return redirect()->route('especialidades.index')
            ->with('success', 'Especialidad registrada correctamente');
    }

    public function edit(Especialidad $especialidad)
    {
        return view('especialidades.edit', compact('especialidad'));
    }

    public function update(Request $request, Especialidad $especialidad)
    {
        $validated = $request->validate([
            'nombre' => 'required|unique:especialidades,nombre,' . $especialidad->id,
            'descripcion' => 'nullable|string',
            'estado' => 'required|boolean'
        ]);

        $especialidad->update($validated);

        return redirect()->route('especialidades.index')
            ->with('success', 'Especialidad actualizada correctamente');
    }

    public function destroy(Especialidad $especialidad)
    {
        /**
         * 👇 NUEVO: doble protección. Primero un chequeo explícito con
         * mensaje claro (mejor experiencia), y el try/catch como red de
         * seguridad por si la FK de la BD (ahora restrictOnDelete) lo
         * rechaza de todas formas.
         */
        if ($especialidad->medicos()->exists()) {
            return back()->with('error', 'No se puede eliminar esta especialidad porque tiene médicos asociados.');
        }

        try {
            $especialidad->delete();
        } catch (QueryException $e) {
            return back()->with('error', 'No se puede eliminar esta especialidad porque está en uso.');
        }

        return redirect()->route('especialidades.index')
            ->with('success', 'Especialidad eliminada correctamente');
    }
}