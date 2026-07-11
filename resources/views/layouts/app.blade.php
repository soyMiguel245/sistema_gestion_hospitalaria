<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Gestión Hospitalaria</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 240px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #08024a;
            padding-top: 10px;
        }

        /* ===== CABECERA DEL MENÚ ===== */
        .menu-header {
            text-align: center;
            padding: 15px 10px;
            color: #ffffff;
        }

        .menu-header i {
            font-size: 2rem;
            color: #4fa1ed;
        }

        .menu-header span {
            display: block;
            margin-top: 6px;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 1px;
            line-height: 1.2;
        }

        .sidebar a {
            color: #cbd5e1;
            padding: 12px 20px;
            display: block;
            text-decoration: none;
            font-size: 15px;
            transition: all .2s;
        }

        .sidebar a i {
            margin-right: 10px;
            font-size: 1.1rem;
        }

        .sidebar a:hover {
            background-color: #0715ad;
            color: #fff;
        }

        .sidebar .collapse a {
            font-size: 14px;
            padding: 8px 35px;
            color: #6481a8;
        }

        .sidebar .collapse a:hover {
            color: #fff;
            background-color: #272efc;
        }

        /* ===== CONTENIDO ===== */
        .content {
            margin-left: 240px;
            padding: 30px;
        }

        /* ===== ICONOS ===== */
        .icon-dashboard { color:#0dcaf0; }
        .icon-paciente  { color:#0d6efd; }
        .icon-cita      { color:#20c997; }
        .icon-historia  { color:#6f42c1; }
        .icon-atencion  { color:#dc3545; }
        .icon-reporte   { color:#fd7e14; }



     /* ===== CERRAR SESIÓN ===== */
.logout-container {
    position: absolute;
    bottom: 20px;
    left: 0;
    width: 240px; /* mismo ancho del sidebar */
    padding: 0 15px;
}

.logout-link {
    display: block;
    background-color:rgb(21, 5, 99);
    color: #fff;
    padding: 12px 20px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 15px;
    transition: background .2s;
}

.logout-link i {
    margin-right: 10px;
}

.logout-link:hover {
    background-color:rgb(43, 7, 187);
    color: #fff;
}


    </style>
</head>

<body>

<!-- ===== SIDEBAR ===== -->
<div class="sidebar">

    <!-- CABECERA DEL SISTEMA -->
    <div class="menu-header">
        <i class="bi bi-hospital"></i>
        <span>SISTEMA DE GESTIÓN<br>HOSPITALARIA</span>
    </div>

    <a href="{{ route('dashboard') }}">
        <i class="bi bi-speedometer2 icon-dashboard"></i> Dashboard
    </a>

    <!-- PACIENTES -->
    <a data-bs-toggle="collapse" href="#pacientesMenu" role="button">
        <i class="bi bi-person-fill icon-paciente"></i> Pacientes
    </a>
    <div class="collapse" id="pacientesMenu">
        <a href="{{ route('pacientes.index') }}">• Listado</a>
        <a href="{{ route('pacientes.create') }}">• Agregar</a>
    </div>

    <!-- CITAS -->
    <a data-bs-toggle="collapse" href="#citasMenu" role="button">
        <i class="bi bi-calendar-check-fill icon-cita"></i> Citas Médicas
    </a>
    <div class="collapse" id="citasMenu">
        <a href="{{ route('citas.index') }}">• Listado</a>
        <a href="{{ route('citas.create') }}">• Agregar</a>
    </div>

    <!-- HISTORIAS -->
    <a data-bs-toggle="collapse" href="#historiasMenu" role="button">
        <i class="bi bi-file-medical-fill icon-historia"></i> Historias Clínicas
    </a>
    <div class="collapse" id="historiasMenu">
        <a href="{{ route('historias.index') }}">• Listado</a>
        <a href="{{ route('historias.create') }}">• Agregar</a>
    </div>

    <!-- ATENCIONES -->
    <a data-bs-toggle="collapse" href="#atencionesMenu" role="button">
        <i class="bi bi-heart-pulse-fill icon-atencion"></i> Atenciones
    </a>
    <div class="collapse" id="atencionesMenu">
        <a href="{{ route('atenciones.index') }}">• Listado</a>
        <a href="{{ route('atenciones.create') }}">• Agregar</a>
    </div>

    <!-- REPORTES -->
    <a href="{{ route('reportes.index') }}">
        <i class="bi bi-bar-chart-fill icon-reporte"></i> Reportes
    </a>

</div>

<!-- ===== CONTENIDO ===== -->
<div class="content">
    @yield('content')
</div>

<!-- Bootstrap JS (IMPRESCINDIBLE PARA COLLAPSE) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

<!-- ===== CERRAR SESIÓN ===== -->
<div class="logout-container">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-link w-100 border-0 text-start">
            <i class="bi bi-box-arrow-right"></i>
            Cerrar sesión
        </button>
    </form>
</div>


</html>
