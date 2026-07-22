<?php

namespace App\Http\Controllers;

use App\Models\AtencionMedica;
use App\Models\ArchivoMedico;
use App\Models\Paciente;
use App\Models\Cita;
use App\Models\Medico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AtencionMedicaController extends Controller
{
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
            'medico_id' => 'required|exists:medicos,id',
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

        // Los archivos no van en $data (ya no son columnas de atenciones_medicas)
        $examenes = $request->file('examenes_adjuntos', []);
        $imagenes = $request->file('imagenes_medicas', []);

        $atencion = AtencionMedica::create($data);

        $this->guardarArchivos($atencion, $examenes, 'examen');
        $this->guardarArchivos($atencion, $imagenes, 'imagen');

        return redirect()->route('atenciones.index')->with('success', 'Atención Médica registrada correctamente.');
    }

    // Mostrar una atención médica
    public function show(AtencionMedica $atencion)
    {
        $atencion->load(['paciente', 'medico', 'cita', 'archivos']);
        return view('atenciones.show', compact('atencion'));
    }

    // Formulario para editar
    public function edit(AtencionMedica $atencion)
    {
        $pacientes = Paciente::where('estado', 'Activo')->get();
        $citas = Cita::where('estado', 'Programada')->get();
        $medicos = Medico::where('estado', 1)->orderBy('apellidos')->orderBy('nombres')->get();
        $atencion->load('archivos');

        return view('atenciones.edit', compact('atencion', 'pacientes', 'citas', 'medicos'));
    }

    // Actualizar atención médica
    public function update(Request $request, AtencionMedica $atencion)
    {
        $data = $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'cita_id' => 'nullable|exists:citas,id',
            'medico_id' => 'required|exists:medicos,id',
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
            // 👇 NUEVO respecto al original: ahora sí se pueden agregar más
            // archivos al editar (antes update() no tenía esta opción).
            'examenes_adjuntos.*' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'imagenes_medicas.*' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'tipo_paciente' => 'required|in:Particular,Seguro,Convenio',
            'costo' => 'nullable|numeric',
            'descuento' => 'nullable|numeric',
            'estado_pago' => 'required|in:Pendiente,Pagado,Exonerado',
            'numero_autorizacion' => 'nullable|string|max:100',
            'estado' => 'required|in:Pendiente,En Progreso,Atendido,Derivado,Alta',
            'proxima_cita' => 'nullable|date',
            'alta_medica' => 'nullable|boolean',
        ]);

        $examenes = $request->file('examenes_adjuntos', []);
        $imagenes = $request->file('imagenes_medicas', []);
        unset($data['examenes_adjuntos'], $data['imagenes_medicas']);

        $atencion->update($data);

        $this->guardarArchivos($atencion, $examenes, 'examen');
        $this->guardarArchivos($atencion, $imagenes, 'imagen');

        return redirect()->route('atenciones.index')->with('success', 'Atención Médica actualizada correctamente.');
    }

    /**
     * 👇 NUEVO: elimina un archivo puntual (no toda la atención).
     * Antes no existía forma de borrar un solo archivo sin reescribir
     * el array JSON completo a mano.
     */
    public function destroyArchivo(ArchivoMedico $archivo)
    {
        $this->authorize('update', $archivo->atencionMedica);

        Storage::disk('public')->delete($archivo->ruta);
        $archivo->delete();

        return back()->with('success', 'Archivo eliminado correctamente.');
    }

    // Eliminar atención médica
    public function destroy(AtencionMedica $atencion)
    {
        // Los archivos físicos se borran uno por uno; las filas de
        // archivos_medicos se borran solas por el cascadeOnDelete() de la FK.
        foreach ($atencion->archivos as $archivo) {
            Storage::disk('public')->delete($archivo->ruta);
        }

        $atencion->delete();

        return redirect()->route('atenciones.index')->with('success', 'Atención Médica eliminada correctamente.');
    }

    /**
     * Guarda un conjunto de archivos subidos (de un tipo dado) como filas
     * en archivos_medicos, con su metadata completa.
     *
     * @param  \Illuminate\Http\UploadedFile[]  $archivos
     */
    private function guardarArchivos(AtencionMedica $atencion, array $archivos, string $tipo): void
    {
        $carpeta = $tipo === 'examen' ? 'examenes' : 'imagenes_medicas';

        foreach ($archivos as $file) {
            if (! $file) {
                continue;
            }

            $ruta = $file->store($carpeta, 'public');

            ArchivoMedico::create([
                'atencion_medica_id' => $atencion->id,
                'tipo' => $tipo,
                'ruta' => $ruta,
                'nombre_original' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'tamano_bytes' => $file->getSize(),
                'subido_por' => Auth::id(),
            ]);
        }
    }
}