<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TwoFactorChallengeController extends Controller
{
    /**
     * Muestra el formulario para ingresar el código de 6 dígitos.
     * Solo accesible si ya se pasó la contraseña correctamente
     * (ver AuthenticatedSessionController::store), nunca directo.
     */
    public function create(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('login.2fa.user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    /**
     * Verifica el código (o un código de recuperación) y, si es
     * correcto, recién ahí completa el login.
     */
    public function store(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('login.2fa.user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::findOrFail($userId);

        $request->validate([
            'codigo' => 'nullable|string',
            'codigo_recuperacion' => 'nullable|string',
        ]);

        $valido = false;

        if ($request->filled('codigo')) {
            $valido = $user->verificarCodigoDosFactores($request->input('codigo'));
        } elseif ($request->filled('codigo_recuperacion')) {
            $valido = $user->usarCodigoRecuperacion($request->input('codigo_recuperacion'));
        }

        if (! $valido) {
            return back()->withErrors(['codigo' => 'El código no es válido o ya expiró.']);
        }

        $request->session()->forget('login.2fa.user_id');

        Auth::login($user, $request->session()->pull('login.2fa.remember', false));
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }
}
