<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TwoFactorController extends Controller
{
    /**
     * Inicia la activación: genera el secreto y muestra el QR.
     * El 2FA NO queda activo todavía — falta confirmar con un código
     * real generado por la app del celular, para evitar que un typo
     * en la configuración deje al usuario bloqueado de su cuenta.
     */
    public function activar(Request $request): RedirectResponse
    {
        $request->user()->generarSecretoDosFactores();

        return redirect()->route('profile.edit')
            ->with('status', 'two-factor-generado');
    }

    /**
     * Confirma la activación con un código de 6 dígitos.
     */
    public function confirmar(Request $request): RedirectResponse
    {
        $request->validate(['codigo' => 'required|string']);

        $user = $request->user();

        if (! $user->verificarCodigoDosFactores($request->input('codigo'))) {
            return back()->withErrors(['codigo' => 'El código ingresado no es válido.']);
        }

        $codigosRecuperacion = $user->confirmarDosFactores();

        return redirect()->route('profile.edit')
            ->with('status', 'two-factor-confirmado')
            ->with('codigosRecuperacion', $codigosRecuperacion);
    }

    /**
     * Desactiva el 2FA. Pide la contraseña actual como confirmación,
     * ya que es una acción que reduce la seguridad de la cuenta.
     */
    public function desactivar(Request $request): RedirectResponse
    {
        $request->validate(['password' => 'required|string']);

        if (! Hash::check($request->input('password'), $request->user()->password)) {
            return back()->withErrors(['password' => 'La contraseña no es correcta.']);
        }

        $request->user()->desactivarDosFactores();

        return redirect()->route('profile.edit')->with('status', 'two-factor-desactivado');
    }
}
