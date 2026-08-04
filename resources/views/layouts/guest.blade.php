<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión Hospitalaria - @yield('title', 'Acceso')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #08024a;
            font-family: Arial, sans-serif;
        }
        .guest-card {
            background: #fff;
            border-radius: 10px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 10px 30px rgba(0,0,0,.2);
        }
        .guest-header {
            text-align: center;
            margin-bottom: 25px;
        }
        .guest-header i {
            font-size: 2.5rem;
            color: #4fa1ed;
        }
        .guest-header span {
            display: block;
            margin-top: 6px;
            font-weight: bold;
            letter-spacing: 1px;
            color: #08024a;
        }
    </style>
</head>
<body>

    <div class="guest-card">
        <div class="guest-header">
            <i class="bi bi-hospital"></i>
            <span>SISTEMA DE GESTIÓN<br>HOSPITALARIA</span>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

      @yield('content', $slot ?? '')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>