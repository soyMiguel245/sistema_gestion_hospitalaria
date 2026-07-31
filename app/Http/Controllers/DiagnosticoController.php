<?php

namespace App\Http\Controllers;

use App\Models\Diagnostico;
use App\Models\AtencionMedica;
use Illuminate\Http\Request;

class DiagnosticoController extends Controller
{
    /**
     * Conecta cada acción con DiagnosticoPolicy. Se agrega ahora, de forma
     * preventiva, aunque el módulo todavía no tiene rutas activas — así,
     * cuando se agreguen, ya van a estar protegidas desde el día uno.
     */
    public function __construct()
    {
        $this->authorizeResource(Diagnostico::class, 'diagnostico');
    }

    public function index()
    {
        $diagnosticos = Diagnostico::with('atencionMedica')->paginate(10);
        return view('diagnosticos.index', compact('diagnosticos'));
    }

    public function create()
    {
        $atenciones = AtencionMedica::all();
        return view('diagnosticos.create', compact('atenciones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'atencion_medica_id' => 'required|exists:atenciones_medicas,id',
            'descripcion' => 'required|string',
            'tipo' => 'required|in:Principal,Secundario',
            'cie10' => 'nullable|string|max:10',
            'observaciones' => 'nullable|string|max:500',
        ]);

        Diagnostico::create($validated);

        return redirect()->route('diagnosticos.index')
            ->with('success', 'Diagnóstico registrado correctamente');
    }

    public function edit(Diagnostico $diagnostico)
    {
        $atenciones = AtencionMedica::all();
        return view('diagnosticos.edit', compact('diagnostico', 'atenciones'));
    }

    public function update(Request $request, Diagnostico $diagnostico)
    {
        $validated = $request->validate([
            'atencion_medica_id' => 'required|exists:atenciones_medicas,id',
            'descripcion' => 'required|string',
            'tipo' => 'required|in:Principal,Secundario',
            'cie10' => 'nullable|string|max:10',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $diagnostico->update($validated);

        return redirect()->route('diagnosticos.index')
            ->with('success', 'Diagnóstico actualizado correctamente');
    }

    public function destroy(Diagnostico $diagnostico)
    {
        $diagnostico->delete();
        return redirect()->route('diagnosticos.index')
            ->with('success', 'Diagnóstico eliminado correctamente');
    }
}