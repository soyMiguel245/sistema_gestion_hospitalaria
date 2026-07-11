<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Hospitalario - @yield('title')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome para iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- CSS propio -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: row;
        }
        .sidebar {
            width: 220px;
            background-color: #0d6efd;
            min-height: 100vh;
            color: #fff;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 12px 20px;
        }
        .sidebar a:hover {
            background-color: #0b5ed7;
            text-decoration: none;
        }
        .content {
            flex-grow: 1;
            padding: 20px;
        }
        .sidebar-header {
            font-size: 1.5rem;
            font-weight: bold;
            padding: 20px;
            text-align: center;
            background-color: #0b5ed7;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            Hospital
        </div>
        <a href="{{ route('pacientes.index') }}"><i class="fa-solid fa-user-injured"></i> Pacientes</a>
        <a href="#"><i class="fa-solid fa-calendar-check"></i> Citas</a>
        <a href="#"><i class="fa-solid fa-file-medical"></i> Historias Clínicas</a>
        <a href="#"><i class="fa-solid fa-stethoscope"></i> Atenciones</a>
        <a href="#"><i class="fa-solid fa-chart-line"></i> Reportes</a>
    </div>

    <!-- Contenido principal -->
    <div class="content">
        <h1>@yield('title')</h1>
        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
