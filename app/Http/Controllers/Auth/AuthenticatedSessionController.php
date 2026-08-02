<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        // 👇 NUEVO: si el usuario tiene 2FA activo, NO completamos el
        // login todavía. Lo desloggeamos de inmediato (Auth::attempt ya
        // lo había logueado dentro de authenticate()) y lo mandamos a la
        // pantalla de código, guardando solo su ID en sesión mientras
        // tanto. El login solo se completa en TwoFactorChallengeController
        // si el código de 6 dígitos es correcto.
        if ($user && $user->tieneDosFactoresActivo()) {
            $remember = $request->boolean('remember');

            Auth::logout();

            $request->session()->put('login.2fa.user_id', $user->id);
            $request->session()->put('login.2fa.remember', $remember);

            return redirect()->route('two-factor.challenge');
        }

        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
