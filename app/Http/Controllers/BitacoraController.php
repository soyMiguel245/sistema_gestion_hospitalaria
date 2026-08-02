<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class BitacoraController extends Controller
{
    /**
     * 👇 Solo Administrador puede ver la bitácora de auditoría —
     * contiene el rastro de quién hizo qué en todo el sistema.
     */
    public function __construct()
    {
        $this->middleware(function (Request $request, $next) {
            if (! $request->user() || ! $request->user()->hasRole('administrador')) {
                abort(403, 'Solo el administrador puede ver la bitácora de auditoría.');
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $registros = Activity::with('causer')
            ->when($request->filled('modulo'), function ($query) use ($request) {
                $query->where('log_name', $request->input('modulo'));
            })
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('bitacora.index', compact('registros'));
    }
}
