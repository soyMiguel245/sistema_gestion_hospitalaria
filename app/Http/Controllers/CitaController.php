<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Paciente;
use App\Rules\MedicoDisponible;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CitaController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Cita::class, 'cita');
    }

    // Mostrar listado de citas
    public function index(Request $request)
    {
        $medicos = Medico::where('estado', 1)
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();

        $citas = Cita::with(['paciente', 'medico', 'especialidad'])
            ->when($request->medico_id, function ($q) use ($request) {
                $q->where('medico_id', $request->medico_id);
            })
            ->when($request->fecha, function ($q) use ($request) {
                $q->whereDate('fecha_hora', $request->fecha);
            })
            ->orderBy('fecha_hora')
            ->get();

        return view('citas.index', compact('citas', 'medicos'));
    }

    // Formulario para crear nueva cita
    public function create()
    {
        $pacientes = Paciente::orderBy('apellidos')->orderBy('nombres')->get();
        $medicos = Medico::where('estado', 1)->orderBy('apellidos')->orderBy('nombres')->get();
        $especialidades = Especialidad::where('estado', 1)->orderBy('nombre')->get();

        return view('citas.create', compact('pacientes', 'medicos', 'especialidades'));
    }

    // Guardar nueva cita
    public function store(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'medico_id' => 'required|exists:medicos,id',
            'especialidad_id' => 'required|exists:especialidades,id',
            // 👇 DÍA 9: valida que el médico no tenga otra cita que choque
            // con este horario (considerando la duración de ambas citas).
            'fecha_hora' => [
                'required',
                new MedicoDisponible(
                    medicoId: $request->integer('medico_id') ?: null,
                    fechaHora: $request->input('fecha_hora'),
                    duracionMinutos: $request->integer('duracion', 30),
                ),
            ],
            'turno' => 'required',
            'tipo_cita' => 'required',
            'origen' => 'required',
            'area_servicio' => 'required',
            'prioridad' => 'required',
            'motivo' => 'required',
            'tipo_paciente' => 'required',
            'estado' => 'required',
        ]);

        Cita::create([
            'paciente_id' => $request->paciente_id,
            'medico_id' => $request->medico_id,
            'especialidad_id' => $request->especialidad_id,
            'fecha_hora' => Carbon::parse($request->fecha_hora)->format('Y-m-d H:i:s'),
            'duracion' => $request->integer('duracion', 30),
            'turno' => $request->turno,
            'tipo_cita' => $request->tipo_cita,
            'origen' => $request->origen,
            'area_servicio' => $request->area_servicio,
            'prioridad' => $request->prioridad,
            'motivo' => $request->motivo,
            'tipo_paciente' => $request->tipo_paciente,
            'estado' => $request->estado,
            'codigo_cita' => 'CITA-'.now()->timestamp,
            'confirmada' => false,
        ]);

        return redirect()->route('citas.index')
            ->with('success', 'Cita registrada correctamente');
    }

    // Ver detalles de una cita
    public function show(Cita $cita)
    {
        $cita->load(['paciente', 'medico', 'especialidad']);

        return view('citas.show', compact('cita'));
    }

    // Formulario para editar cita
    public function edit(Cita $cita)
    {
        $pacientes = Paciente::orderBy('apellidos')->orderBy('nombres')->get();
        $medicos = Medico::where('estado', 1)->orderBy('apellidos')->orderBy('nombres')->get();
        $especialidades = Especialidad::where('estado', 1)->orderBy('nombre')->get();

        return view('citas.edit', compact('cita', 'pacientes', 'medicos', 'especialidades'));
    }

    // Actualizar cita
    public function update(Request $request, Cita $cita)
    {
        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'medico_id' => 'required|exists:medicos,id',
            'especialidad_id' => 'required|exists:especialidades,id',
            'fecha_hora' => [
                'required',
                new MedicoDisponible(
                    medicoId: $request->integer('medico_id') ?: null,
                    fechaHora: $request->input('fecha_hora'),
                    duracionMinutos: $request->integer('duracion', 30),
                    ignorarCitaId: $cita->id, // no chocar contra sí misma
                ),
            ],
            'turno' => 'required',
            'tipo_cita' => 'required',
            'origen' => 'required',
            'area_servicio' => 'required',
            'prioridad' => 'required',
            'motivo' => 'required',
            'tipo_paciente' => 'required',
            'estado' => 'required',
        ]);

        $cita->update([
            'paciente_id' => $request->paciente_id,
            'medico_id' => $request->medico_id,
            'especialidad_id' => $request->especialidad_id,
            'fecha_hora' => Carbon::parse($request->fecha_hora)->format('Y-m-d H:i:s'),
            'duracion' => $request->integer('duracion', 30),
            'turno' => $request->turno,
            'tipo_cita' => $request->tipo_cita,
            'origen' => $request->origen,
            'area_servicio' => $request->area_servicio,
            'prioridad' => $request->prioridad,
            'motivo' => $request->motivo,
            'tipo_paciente' => $request->tipo_paciente,
            'estado' => $request->estado,
        ]);

        return redirect()->route('citas.index')
            ->with('success', 'Cita actualizada correctamente');
    }

    // Eliminar cita
    public function destroy(Cita $cita)
    {
        $cita->delete();

        return redirect()->route('citas.index')
            ->with('success', 'Cita eliminada correctamente');
    }
}
