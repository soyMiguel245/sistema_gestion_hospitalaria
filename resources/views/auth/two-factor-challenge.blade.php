<x-guest-layout>
    <div class="text-center mb-4">
        <i class="bi bi-shield-lock-fill" style="font-size: 2.5rem; color: #4fa1ed;"></i>
        <h2 class="mt-2" style="color: #08024a; font-weight: bold;">Verificación en dos pasos</h2>
        <p class="text-muted small">
            Abre tu app de autenticación (Google Authenticator, Authy, etc.)
            e ingresa el código de 6 dígitos.
        </p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('two-factor.challenge.store') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Código de 6 dígitos</label>
            <input type="text" name="codigo" inputmode="numeric" maxlength="6" autofocus
                   class="form-control form-control-lg text-center" style="letter-spacing: 8px;">
        </div>

        <button type="submit" class="btn btn-primary w-100">Verificar</button>
    </form>

    <hr class="my-4">

    <details>
        <summary class="text-muted small" style="cursor: pointer;">
            ¿Perdiste acceso a tu app de autenticación?
        </summary>
        <form method="POST" action="{{ route('two-factor.challenge.store') }}" class="mt-3">
            @csrf
            <label class="form-label small">Usa uno de tus códigos de recuperación</label>
            <input type="text" name="codigo_recuperacion" class="form-control">
            <button type="submit" class="btn btn-outline-secondary w-100 mt-2">
                Usar código de recuperación
            </button>
        </form>
    </details>
</x-guest-layout>