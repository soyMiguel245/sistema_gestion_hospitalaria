<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso | Sistema Hospitalario</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(120deg, #0d6efd, #198754);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: #fff;
            border-radius: 14px;
            padding: 30px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 15px 40px rgba(0,0,0,.2);
        }

        .login-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .login-header i {
            font-size: 3rem;
            color: #0d6efd;
        }

        .login-header h4 {
            margin-top: 10px;
            font-weight: bold;
            color: #333;
        }

        .btn-hospital {
            background: #0d6efd;
            border: none;
        }

        .btn-hospital:hover {
            background: #0b5ed7;
        }
    </style>
</head>

<body>

<div class="login-card">
    <div class="login-header">
        <i class="bi bi-hospital"></i>
        <h4>Sistema de Gestión Hospitalaria</h4>
        <small class="text-muted">Acceso al sistema</small>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div class="mb-3">
            <label class="form-label">Correo electrónico</label>
            <input type="email" name="email" class="form-control" required autofocus>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <!-- Remember -->
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="remember">
            <label class="form-check-label">Recordarme</label>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('password.request') }}" class="text-decoration-none">
                ¿Olvidaste tu contraseña?
            </a>

            <button type="submit" class="btn btn-hospital text-white px-4">
                <i class="bi bi-box-arrow-in-right"></i> Acceder
            </button>
        </div>
    </form>
</div>

</body>
</html>
