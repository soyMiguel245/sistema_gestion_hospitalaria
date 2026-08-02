<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PacienteController extends Controller
{
    /**
     * Conecta cada acción con PacientePolicy: index->viewAny, create/store->create,
     * show->view, edit/update->update, destroy->delete.
     */
    public function __construct()
    {
        $this->authorizeResource(Paciente::class, 'paciente');
    }

    public function index()
    {
        $pacientes = Paciente::orderBy('id', 'desc')->get();

        return view('pacientes.index', compact('pacientes'));
    }

    public function create()
    {
        return view('pacientes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'dni' => 'required|unique:pacientes,dni|max:15',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'required|in:Masculino,Femenino,Otro',

            'telefono' => 'nullable|max:20',
            'correo' => 'nullable|email|max:100',
            'direccion' => 'nullable|max:255',

            'estado_civil' => 'nullable|max:50',
            'nacionalidad' => 'nullable|max:50',

            'contacto_emergencia' => 'nullable|max:100',
            'telefono_emergencia' => 'nullable|max:20',

            'tipo_sangre' => 'nullable|max:5',
            'alergias' => 'nullable|max:255',
            'enfermedades_cronicas' => 'nullable|max:255',
            'observaciones' => 'nullable|max:500',

            'tipo_seguro' => 'nullable|in:SIS,ESSALUD,Privado',
            'estado' => 'nullable|in:Activo,Inactivo',
        ]);

        Paciente::create([
            'dni' => $validated['dni'],
            'numero_historia_clinica' => 'HC-'.now()->timestamp,

            'nombres' => $validated['nombres'],
            'apellidos' => $validated['apellidos'],
            'fecha_nacimiento' => $validated['fecha_nacimiento'],
            'sexo' => $validated['sexo'],
            'estado_civil' => $validated['estado_civil'] ?? null,
            'nacionalidad' => $validated['nacionalidad'] ?? null,

            'telefono' => $validated['telefono'] ?? null,
            'correo' => $validated['correo'] ?? null,
            'direccion' => $validated['direccion'] ?? null,

            'contacto_emergencia' => $validated['contacto_emergencia'] ?? null,
            'telefono_emergencia' => $validated['telefono_emergencia'] ?? null,

            'tipo_sangre' => $validated['tipo_sangre'] ?? null,
            'alergias' => $validated['alergias'] ?? null,
            'enfermedades_cronicas' => $validated['enfermedades_cronicas'] ?? null,
            'observaciones' => $validated['observaciones'] ?? null,

            'tipo_seguro' => $validated['tipo_seguro'] ?? null,
            'estado' => $validated['estado'] ?? 'Activo',
            'fecha_registro' => now(),
        ]);

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente registrado correctamente');
    }

    public function show(Paciente $paciente)
    {
        return view('pacientes.show', compact('paciente'));
    }

    public function edit(Paciente $paciente)
    {
        return view('pacientes.edit', compact('paciente'));
    }

    public function update(Request $request, Paciente $paciente)
    {
        $validated = $request->validate([
            'dni' => [
                'required',
                'max:15',
                Rule::unique('pacientes', 'dni')->ignore($paciente->id),
            ],
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'required|in:Masculino,Femenino,Otro',

            'telefono' => 'nullable|max:20',
            'correo' => 'nullable|email|max:100',
            'direccion' => 'nullable|max:255',

            'estado_civil' => 'nullable|max:50',
            'nacionalidad' => 'nullable|max:50',

            'contacto_emergencia' => 'nullable|max:100',
            'telefono_emergencia' => 'nullable|max:20',

            'tipo_sangre' => 'nullable|max:5',
            'alergias' => 'nullable|max:255',
            'enfermedades_cronicas' => 'nullable|max:255',
            'observaciones' => 'nullable|max:500',

            'tipo_seguro' => 'nullable|in:SIS,ESSALUD,Privado',
            'estado' => 'nullable|in:Activo,Inactivo',
        ]);

        $paciente->update($validated);

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente actualizado correctamente');
    }

    public function destroy(Paciente $paciente)
    {
        $paciente->delete();

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente eliminado correctamente');
    }
}
