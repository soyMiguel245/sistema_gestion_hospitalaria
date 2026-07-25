<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Autenticación en dos pasos (2FA)
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            Agrega una capa extra de seguridad a tu cuenta pidiendo un código
            de tu celular además de la contraseña al iniciar sesión.
        </p>
    </header>

    @if (session('status') === 'two-factor-confirmado' && session('codigosRecuperacion'))
        <div class="mt-4 p-4 bg-yellow-50 border border-yellow-300 rounded">
            <p class="font-semibold text-yellow-800">
                ¡2FA activado! Guarda estos códigos de recuperación en un lugar seguro.
            </p>
            <p class="text-sm text-yellow-700 mb-2">
                Cada uno sirve una sola vez, por si pierdes acceso a tu app de autenticación.
                No se van a volver a mostrar.
            </p>
            <div class="grid grid-cols-2 gap-2 font-mono text-sm bg-white p-3 rounded">
                @foreach (session('codigosRecuperacion') as $codigo)
                    <span>{{ $codigo }}</span>
                @endforeach
            </div>
        </div>
    @endif

    @if ($user->tieneDosFactoresActivo())
        {{-- Estado: 2FA ya activo --}}
        <div class="mt-4 flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                ✓ Activo desde {{ $user->two_factor_confirmed_at->format('d/m/Y') }}
            </span>
        </div>

        <form method="POST" action="{{ route('two-factor.disable') }}" class="mt-4 max-w-sm">
            @csrf
            @method('DELETE')
            <label class="block text-sm text-gray-700 mb-1">
                Ingresa tu contraseña para desactivarlo
            </label>
            <input type="password" name="password" class="border-gray-300 rounded-md shadow-sm w-full">
            @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            <button type="submit" class="mt-2 bg-red-600 text-white px-4 py-2 rounded-md text-sm">
                Desactivar 2FA
            </button>
        </form>

    @elseif ($user->two_factor_secret)
        {{-- Estado: secreto generado, esperando confirmación con el QR --}}
        <div class="mt-4">
            <p class="text-sm text-gray-700 mb-2">
                Escanea este código con Google Authenticator, Authy, o cualquier
                app compatible con TOTP:
            </p>

            <div class="p-4 bg-white inline-block border rounded">
                {!! (new \BaconQrCode\Writer(
                        new \BaconQrCode\Renderer\ImageRenderer(
                            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200),
                            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
                        )
                    ))->writeString($user->urlQrDosFactores()) !!}
            </div>

            <p class="text-xs text-gray-500 mt-2">
                ¿No puedes escanear? Ingresa este código manualmente:
                <code class="bg-gray-100 px-1">{{ $user->two_factor_secret }}</code>
            </p>

            <form method="POST" action="{{ route('two-factor.confirm') }}" class="mt-4 max-w-sm">
                @csrf
                <label class="block text-sm text-gray-700 mb-1">
                    Ingresa el código de 6 dígitos que muestra la app para confirmar
                </label>
                <input type="text" name="codigo" maxlength="6" inputmode="numeric"
                       class="border-gray-300 rounded-md shadow-sm w-full text-center tracking-widest">
                @error('codigo') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                <button type="submit" class="mt-2 bg-green-600 text-white px-4 py-2 rounded-md text-sm">
                    Confirmar y activar
                </button>
            </form>
        </div>

    @else
        {{-- Estado: nunca activado --}}
        <form method="POST" action="{{ route('two-factor.enable') }}" class="mt-4">
            @csrf
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm">
                Activar autenticación en dos pasos
            </button>
        </form>
    @endif
</section>