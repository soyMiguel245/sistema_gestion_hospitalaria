<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sistema de Gestión Hospitalaria</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            min-height: 100vh;
            margin: 0;
            font-family: 'Figtree', sans-serif;

            /* IMAGEN DE FONDO HOSPITAL */
            background-image: url('https://images.unsplash.com/photo-1586773860418-d37222d8fce3');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;

            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* CAPA OSCURA PARA MEJOR LECTURA */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            z-index: 0;
        }

        .auth-card {
            position: relative;
            z-index: 1;
            background: #ffffff;
            width: 100%;
            max-width: 420px;
            border-radius: 14px;
            padding: 30px;
            box-shadow: 0 25px 50px rgba(0,0,0,.35);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .auth-header i {
            font-size: 3rem;
            color: #0d6efd;
        }

        .auth-header h4 {
            margin-top: 10px;
            font-weight: 700;
            color: #1f2937;
        }

        .auth-header span {
            font-size: 14px;
            color: #6b7280;
        }

        .btn-primary {
            background-color: #0d6efd !important;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0b5ed7 !important;
        }
    </style>
</head>

<body>

    <div class="auth-card">

        <!-- CABECERA -->
        <div class="auth-header">
            <i class="bi bi-hospital"></i>
            <h4>Sistema Hospitalario</h4>
            <span>Acceso seguro al sistema</span>
        </div>

        <!-- LOGIN / REGISTRO -->
        {{ $slot }}

    </div>

</body>
</html>
