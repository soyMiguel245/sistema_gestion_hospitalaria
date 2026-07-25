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
use Illuminate\Support\Facades\DB;

class AtencionMedicaController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(AtencionMedica::class, 'atencion');
    }

    public function index()
    {
        $atenciones = AtencionMedica::with(['paciente', 'medico', 'cita'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('atenciones.index', compact('atenciones'));
    }

    public function create()
    {
        $pacientes = Paciente::where('estado', 'Activo')->get();
        $citas = Cita::where('estado', 'Programada')->get();
        $medicos = Medico::where('estado', 1)->orderBy('apellidos')->orderBy('nombres')->get();

        return view('atenciones.create', compact('pacientes', 'citas', 'medicos'));
    }

    /**
     * 👇 CORREGIDO (Día 2 del plan): todo el guardado ahora corre dentro de
     * una transacción. Si falla la creación de un archivo a mitad de camino,
     * se revierte TODO (la atención y los archivos ya guardados), en vez de
     * dejar una atención médica a medias sin sus adjuntos, o archivos huérfanos
     * sin atención asociada.
     */
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

        $examenes = $request->file('examenes_adjuntos', []);
        $imagenes = $request->file('imagenes_medicas', []);

        DB::transaction(function () use ($data, $examenes, $imagenes) {
            $atencion = AtencionMedica::create($data);
            $this->guardarArchivos($atencion, $examenes, 'examen');
            $this->guardarArchivos($atencion, $imagenes, 'imagen');
        });

        return redirect()->route('atenciones.index')->with('success', 'Atención Médica registrada correctamente.');
    }

    public function show(AtencionMedica $atencion)
    {
        $atencion->load(['paciente', 'medico', 'cita', 'archivos']);
        return view('atenciones.show', compact('atencion'));
    }

    public function edit(AtencionMedica $atencion)
    {
        $pacientes = Paciente::where('estado', 'Activo')->get();
        $citas = Cita::where('estado', 'Programada')->get();
        $medicos = Medico::where('estado', 1)->orderBy('apellidos')->orderBy('nombres')->get();
        $atencion->load('archivos');

        return view('atenciones.edit', compact('atencion', 'pacientes', 'citas', 'medicos'));
    }

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

        DB::transaction(function () use ($atencion, $data, $examenes, $imagenes) {
            $atencion->update($data);
            $this->guardarArchivos($atencion, $examenes, 'examen');
            $this->guardarArchivos($atencion, $imagenes, 'imagen');
        });

        return redirect()->route('atenciones.index')->with('success', 'Atención Médica actualizada correctamente.');
    }

    /**
     * 👇 NUEVO — el corazón del fix de seguridad: esta es la ÚNICA forma
     * de obtener el contenido de un archivo médico. Verifica permiso con
     * la misma Policy que protege la atención médica dueña del archivo,
     * y solo entonces lo sirve desde el disco privado 'local'.
     */
    public function descargar(ArchivoMedico $archivo)
    {
        $this->authorize('view', $archivo->atencionMedica);

        if (! Storage::disk('local')->exists($archivo->ruta)) {
            abort(404, 'El archivo no existe o fue eliminado.');
        }

        return Storage::disk('local')->response(
            $archivo->ruta,
            $archivo->nombre_original
        );
    }

    public function destroyArchivo(ArchivoMedico $archivo)
    {
        $this->authorize('update', $archivo->atencionMedica);

        Storage::disk('local')->delete($archivo->ruta);
        $archivo->delete();

        return back()->with('success', 'Archivo eliminado correctamente.');
    }

    public function destroy(AtencionMedica $atencion)
    {
        foreach ($atencion->archivos as $archivo) {
            Storage::disk('local')->delete($archivo->ruta);
        }

        $atencion->delete();

        return redirect()->route('atenciones.index')->with('success', 'Atención Médica eliminada correctamente.');
    }

    /**
     * 👇 CORREGIDO: ->store($carpeta, 'public') → ->store($carpeta, 'local').
     * Ahora los archivos se guardan en storage/app/private (fuera de la
     * carpeta pública), inaccesibles por URL directa.
     */
    private function guardarArchivos(AtencionMedica $atencion, array $archivos, string $tipo): void
    {
        $carpeta = $tipo === 'examen' ? 'examenes' : 'imagenes_medicas';

        foreach ($archivos as $file) {
            if (! $file) {
                continue;
            }

            $ruta = $file->store($carpeta, 'local');

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