<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso no autorizado</title>
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
        .error-card {
            background: #fff;
            border-radius: 10px;
            padding: 50px 40px;
            width: 100%;
            max-width: 460px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,.2);
        }
        .error-card i {
            font-size: 3.5rem;
            color: #dc3545;
        }
        .error-card h1 {
            font-size: 1.6rem;
            margin-top: 15px;
            color: #08024a;
        }
        .error-card p {
            color: #6481a8;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <i class="bi bi-shield-lock-fill"></i>
        <h1>Acceso no autorizado</h1>
        <p>
            {{ $exception->getMessage() ?: 'Tu rol actual no tiene permiso para ver esta sección del sistema hospitalario.' }}
        </p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary mt-3">
            <i class="bi bi-house-fill"></i> Volver al Dashboard
        </a>
    </div>
</body>
</html>