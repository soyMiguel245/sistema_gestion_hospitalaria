<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;

/**
 * Reemplaza a HistoriaClinicaController.
 *
 * La "historia clínica" ya no es una entidad que se crea/edita/cierra por
 * separado: es la línea de tiempo de las AtencionMedica de un paciente
 * (ver Paciente::historialClinico()). Por eso este controlador es de
 * SOLO LECTURA — no hay create/store/update/cerrar. Para registrar una
 * nueva entrada en la historia clínica, se crea una AtencionMedica desde
 * AtencionMedicaController; eso automáticamente pasa a formar parte del
 * expediente del paciente.
 */
class HistorialClinicoController extends Controller
{
    /**
     * Mismo control de acceso que tenía HistoriaClinicaPolicy:
     * solo administrador, médico y enfermera pueden ver contenido clínico.
     * Recepción NO debe verlo (requisito RNF-01 del proyecto).
     */
    public function __construct()
    {
        $this->middleware(function (Request $request, $next) {
            if (! $request->user() || ! $request->user()->hasRole(['administrador', 'medico', 'enfermera'])) {
                abort(403, 'No tienes permiso para ver historias clínicas.');
            }

            return $next($request);
        });
    }

    /**
     * Lista de pacientes con al menos una atención médica registrada,
     * es decir, con expediente clínico "abierto". Soporta búsqueda por
     * nombre, apellido o DNI (igual que el index original).
     */
    public function index(Request $request)
    {
        $pacientes = Paciente::has('atencionesMedicas')
            ->withCount('atencionesMedicas')
            ->when($request->filled('buscar'), function ($query) use ($request) {
                $buscar = $request->input('buscar');
                $query->where(function ($q) use ($buscar) {
                    $q->where('nombres', 'like', "%{$buscar}%")
                        ->orWhere('apellidos', 'like', "%{$buscar}%")
                        ->orWhere('dni', 'like', "%{$buscar}%");
                });
            })
            ->orderBy('apellidos')
            ->get();

        return view('historias.index', compact('pacientes'));
    }

    /**
     * Expediente clínico completo de un paciente: todas sus atenciones
     * médicas en orden cronológico, con diagnósticos, médico y cita
     * de cada una ya cargados.
     *
     * 👇 NUEVO: registra en la Bitácora que este usuario consultó el
     * expediente completo de este paciente. LogsActivity (el trait que
     * usan Paciente/Cita/AtencionMedica) solo audita escritura por
     * diseño — la LECTURA de datos clínicos sensibles necesita este
     * registro manual para cumplir con el RNF-01 ("cada acceso queda
     * registrado", no solo cada modificación).
     */
    public function show(Paciente $paciente)
    {
        activity('historial_clinico')
            ->causedBy(auth()->user())
            ->performedOn($paciente)
            ->withProperties(['accion' => 'lectura_expediente_completo'])
            ->log("Consultó el historial clínico completo de {$paciente->nombres} {$paciente->apellidos}");

        $atenciones = $paciente->historialClinico()->get();

        return view('historias.show', compact('paciente', 'atenciones'));
    }
}
