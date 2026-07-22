<?php

namespace App\Http\Controllers;

use App\Models\Medico;
use App\Models\Especialidad;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class MedicoController extends Controller
{
    /**
     * 👇 NUEVO: antes este controlador no tenía NINGÚN control de acceso.
     * Cualquier usuario logueado podía crear/editar/eliminar médicos.
     */
    public function __construct()
    {
        $this->authorizeResource(Medico::class, 'medico');
    }

    public function index()
    {
        $medicos = Medico::with('especialidad')
            ->orderBy('nombres')
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
        $request->validate([
            'nombres' => 'required|string',
            'apellidos' => 'required|string',
            'dni' => 'required|string|unique:medicos,dni',
            'cmp' => 'required|string|unique:medicos,cmp',
            'especialidad_id' => 'required|exists:especialidades,id',
            'estado' => 'required|boolean',
            // 👇 NUEVO: cuenta de acceso opcional. Si se llena el email,
            // se crea el User y se liga automáticamente al médico.
            'crear_usuario' => 'nullable|boolean',
            'email' => 'nullable|required_if:crear_usuario,1|email|unique:users,email',
            'password' => 'nullable|required_if:crear_usuario,1|min:8',
        ]);

        DB::transaction(function () use ($request) {
            $userId = null;

            if ($request->boolean('crear_usuario')) {
                $user = User::create([
                    'name' => $request->nombres . ' ' . $request->apellidos,
                    'email' => $request->email,
                    'password' => $request->password, // el cast 'hashed' lo encripta
                    'role' => 'medico',
                ]);
                $userId = $user->id;
            }

            Medico::create([
                'user_id' => $userId,
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'dni' => $request->dni,
                'cmp' => $request->cmp,
                'especialidad_id' => $request->especialidad_id,
                'estado' => $request->estado,
            ]);
        });

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
        /**
         * 👇 NUEVO: antes, si el médico tenía citas o atenciones, la BD
         * rechazaba el delete (onDelete('no action')) y el usuario veía
         * un error 500 crudo de SQL Server. Ahora se captura y se muestra
         * un mensaje claro.
         */
        try {
            $medico->delete();
        } catch (QueryException $e) {
            return back()->with('error', 'No se puede eliminar este médico porque tiene citas o atenciones médicas registradas.');
        }

        return redirect()->route('medicos.index')
            ->with('success', 'Médico eliminado correctamente');
    }
}