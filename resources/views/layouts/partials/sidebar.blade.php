<!-- resources/views/partials/sidebar.blade.php -->
<!-- Sidebar único: antes estaba copiado idéntico en app.blade.php, dasbhboard.blade.php
     y guest.blade.php. Ahora vive en un solo lugar. -->

     <div class="sidebar">

<div class="menu-header">
    <i class="bi bi-hospital"></i>
    <span>SISTEMA DE GESTIÓN<br>HOSPITALARIA</span>
</div>

<a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
    <i class="bi bi-speedometer2 icon-dashboard"></i> Dashboard
</a>

<!-- PACIENTES -->
<a data-bs-toggle="collapse" href="#pacientesMenu" role="button">
    <i class="bi bi-person-fill icon-paciente"></i> Pacientes
</a>
<div class="collapse {{ request()->routeIs('pacientes.*') ? 'show' : '' }}" id="pacientesMenu">
    <a href="{{ route('pacientes.index') }}">• Listado</a>
    <a href="{{ route('pacientes.create') }}">• Agregar</a>
</div>

<!-- CITAS -->
<a data-bs-toggle="collapse" href="#citasMenu" role="button">
    <i class="bi bi-calendar-check-fill icon-cita"></i> Citas Médicas
</a>
<div class="collapse {{ request()->routeIs('citas.*') ? 'show' : '' }}" id="citasMenu">
    <a href="{{ route('citas.index') }}">• Listado</a>
    <a href="{{ route('citas.create') }}">• Agregar</a>
</div>

<!-- HISTORIAS -->
<a data-bs-toggle="collapse" href="#historiasMenu" role="button">
    <i class="bi bi-file-medical-fill icon-historia"></i> Historias Clínicas
</a>
<div class="collapse {{ request()->routeIs('historias.*') ? 'show' : '' }}" id="historiasMenu">
    <a href="{{ route('historias.index') }}">• Listado</a>
    <a href="{{ route('historias.create') }}">• Agregar</a>
</div>

<!-- ATENCIONES -->
<a data-bs-toggle="collapse" href="#atencionesMenu" role="button">
    <i class="bi bi-heart-pulse-fill icon-atencion"></i> Atenciones
</a>
<div class="collapse {{ request()->routeIs('atenciones.*') ? 'show' : '' }}" id="atencionesMenu">
    <a href="{{ route('atenciones.index') }}">• Listado</a>
    <a href="{{ route('atenciones.create') }}">• Agregar</a>
</div>

<!-- REPORTES -->
<a href="{{ route('reportes.index') }}">
    <i class="bi bi-bar-chart-fill icon-reporte"></i> Reportes
</a>

<!-- CERRAR SESIÓN -->
<div class="logout-container">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-link w-100 border-0 text-start">
            <i class="bi bi-box-arrow-right"></i> Cerrar sesión
        </button>
    </form>
</div>

</div>