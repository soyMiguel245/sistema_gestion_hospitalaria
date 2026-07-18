<?php

namespace App\Http\Controllers;

use App\Models\AtencionMedica;
use App\Models\Paciente;
use App\Models\Cita;
use App\Models\Medico; // ✅ Usamos la tabla medicos
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AtencionMedicaController extends Controller
{
    /**
     * Conecta cada acción con AtencionMedicaPolicy.
     * Ojo: el segundo parámetro es 'atencion' (no 'atencionMedica'), porque
     * así está mapeado el parámetro de ruta en web.php:
     * Route::resource('atenciones', ...)->parameters(['atenciones' => 'atencion'])
     */
    public function __construct()
    {
        $this->authorizeResource(AtencionMedica::class, 'atencion');
    }

    // Listar atenciones médicas
    public function index()
    {
        $atenciones = AtencionMedica::with(['paciente', 'medico', 'cita'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('atenciones.index', compact('atenciones'));
    }

    // Formulario de creación
    public function create()
    {
        $pacientes = Paciente::where('estado', 'Activo')->get();
        $citas = Cita::where('estado', 'Programada')->get();
        $medicos = Medico::where('estado', 1)->orderBy('apellidos')->orderBy('nombres')->get();

        return view('atenciones.create', compact('pacientes', 'citas', 'medicos'));
    }

    // Guardar nueva atención médica
    public function store(Request $request)
    {
        $data = $request->validate([
            'paciente_id' => 'nullable|exists:pacientes,id',
            'cita_id' => 'nullable|exists:citas,id',
            'medico_id' => 'required|exists:medicos,id', // ✅ Validación corregida
            'motivo_consulta' => 'required|string',
            'diagnostico' => 'nullable|string',
            'tratamiento' => 'nullable|string',
            'procedimientos' => 'nullable|string',
            'indicaciones' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'presion_arterial' => 'nullable|string',
            'frecuencia_cardiaca' => 'nullable|integer',
            'frecuencia_respiratoria' => 'nullable|integer',
            'temperatura' => 'nullable|numeric',
            'saturacion_o2' => 'nullable|integer',
            'peso' => 'nullable|numeric',
            'talla' => 'nullable|numeric',
            'imc' => 'nullable|numeric',
            'examenes_adjuntos.*' => 'file|mimes:pdf,jpg,png|max:5120',
            'imagenes_medicas.*' => 'image|mimes:jpg,jpeg,png|max:5120',
            'tipo_paciente' => 'required|in:Particular,Seguro,Convenio',
            'costo' => 'nullable|numeric',
            'descuento' => 'nullable|numeric',
            'estado_pago' => 'required|in:Pendiente,Pagado,Exonerado',
            'numero_autorizacion' => 'nullable|string|max:100',
            'estado' => 'required|in:Pendiente,En Progreso,Atendido,Derivado,Alta',
            'proxima_cita' => 'nullable|date',
            'alta_medica' => 'nullable|boolean',
        ]);

        $data['registrado_por'] = Auth::id();

        // Guardar archivos adjuntos
        if ($request->hasFile('examenes_adjuntos')) {
            $data['examenes_adjuntos'] = [];
            foreach ($request->file('examenes_adjuntos') as $file) {
                $data['examenes_adjuntos'][] = $file->store('examenes', 'public');
            }
        }

        if ($request->hasFile('imagenes_medicas')) {
            $data['imagenes_medicas'] = [];
            foreach ($request->file('imagenes_medicas') as $file) {
                $data['imagenes_medicas'][] = $file->store('imagenes_medicas', 'public');
            }
        }

        AtencionMedica::create($data);

        return redirect()->route('atenciones.index')->with('success', 'Atención Médica registrada correctamente.');
    }

    // Mostrar una atención médica
    public function show(AtencionMedica $atencion)
    {
        $atencion->load(['paciente', 'medico', 'cita']);
        return view('atenciones.show', compact('atencion'));
    }

    // Formulario para editar
    public function edit(AtencionMedica $atencion)
    {
        $pacientes = Paciente::where('estado', 'Activo')->get();
        $citas = Cita::where('estado', 'Programada')->get();
        $medicos = Medico::where('estado', 1)->orderBy('apellidos')->orderBy('nombres')->get();

        return view('atenciones.edit', compact('atencion', 'pacientes', 'citas', 'medicos'));
    }

    // Actualizar atención médica
    public function update(Request $request, AtencionMedica $atencion)
    {
        $data = $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'cita_id' => 'nullable|exists:citas,id',
            'medico_id' => 'required|exists:medicos,id', // ✅ Validación corregida
            'motivo_consulta' => 'required|string',
            'diagnostico' => 'nullable|string',
            'tratamiento' => 'nullable|string',
            'procedimientos' => 'nullable|string',
            'indicaciones' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'presion_arterial' => 'nullable|string',
            'frecuencia_cardiaca' => 'nullable|integer',
            'frecuencia_respiratoria' => 'nullable|integer',
            'temperatura' => 'nullable|numeric',
            'saturacion_o2' => 'nullable|integer',
            'peso' => 'nullable|numeric',
            'talla' => 'nullable|numeric',
            'imc' => 'nullable|numeric',
            'tipo_paciente' => 'required|in:Particular,Seguro,Convenio',
            'costo' => 'nullable|numeric',
            'descuento' => 'nullable|numeric',
            'estado_pago' => 'required|in:Pendiente,Pagado,Exonerado',
            'numero_autorizacion' => 'nullable|string|max:100',
            'estado' => 'required|in:Pendiente,En Progreso,Atendido,Derivado,Alta',
            'proxima_cita' => 'nullable|date',
            'alta_medica' => 'nullable|boolean',
        ]);

        $atencion->update($data);

        return redirect()->route('atenciones.index')->with('success', 'Atención Médica actualizada correctamente.');
    }

    // Eliminar atención médica
    public function destroy(AtencionMedica $atencion)
    {
        if ($atencion->examenes_adjuntos) {
            foreach ($atencion->examenes_adjuntos as $file) {
                Storage::disk('public')->delete($file);
            }
        }

        if ($atencion->imagenes_medicas) {
            foreach ($atencion->imagenes_medicas as $file) {
                Storage::disk('public')->delete($file);
            }
        }

        $atencion->delete();

        return redirect()->route('atenciones.index')->with('success', 'Atención Médica eliminada correctamente.');
    }
}