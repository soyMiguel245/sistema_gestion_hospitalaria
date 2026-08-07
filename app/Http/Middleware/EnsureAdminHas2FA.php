<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminHas2FA
{
    /**
     * 👇 NUEVO: el rol Administrador es la cuenta más crítica del sistema
     * (gestiona médicos, especialidades, bitácora de auditoría, y puede
     * eliminar prácticamente cualquier registro). Se le obliga a activar
     * 2FA antes de poder usar cualquier otro módulo. Solo queda libre su
     * propio perfil (para activarlo ahí mismo), las rutas de 2FA, y el
     * logout — para no dejarlo atrapado sin salida si aún no lo activó.
     */
    private const RUTAS_PERMITIDAS = [
        'profile.edit',
        'profile.update',
        'profile.destroy',
        'two-factor.enable',
        'two-factor.confirm',
        'two-factor.disable',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->hasRole('administrador') && ! $user->tieneDosFactoresActivo()) {
            if (! $request->routeIs(...self::RUTAS_PERMITIDAS)) {
                return redirect()->route('profile.edit')
                    ->with('status', 'two-factor-requerido');
            }
        }

        return $next($request);
    }
}
