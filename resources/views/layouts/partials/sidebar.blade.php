<!-- resources/views/partials/sidebar.blade.php -->
<!-- Ahora sí filtra según el rol real del usuario, usando las Policies. -->

<div class="sidebar">

    <div class="menu-header">
        <i class="bi bi-hospital"></i>
        <span>SISTEMA DE GESTIÓN<br>HOSPITALARIA</span>
    </div>

    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2 icon-dashboard"></i> Dashboard
    </a>

    {{-- PACIENTES --}}
    @can('viewAny', App\Models\Paciente::class)
    <a data-bs-toggle="collapse" href="#pacientesMenu" role="button">
        <i class="bi bi-person-fill icon-paciente"></i> Pacientes
    </a>
    <div class="collapse {{ request()->routeIs('pacientes.*') ? 'show' : '' }}" id="pacientesMenu">
        <a href="{{ route('pacientes.index') }}">• Listado</a>
        @can('create', App\Models\Paciente::class)
        <a href="{{ route('pacientes.create') }}">• Agregar</a>
        @endcan
    </div>
    @endcan

    {{-- CITAS --}}
    @can('viewAny', App\Models\Cita::class)
    <a data-bs-toggle="collapse" href="#citasMenu" role="button">
        <i class="bi bi-calendar-check-fill icon-cita"></i> Citas Médicas
    </a>
    <div class="collapse {{ request()->routeIs('citas.*') ? 'show' : '' }}" id="citasMenu">
        <a href="{{ route('citas.index') }}">• Listado</a>
        @can('create', App\Models\Cita::class)
        <a href="{{ route('citas.create') }}">• Agregar</a>
        @endcan
    </div>
    @endcan

    {{-- HISTORIAS CLÍNICAS: solo médico/administrador la van a ver --}}
    @can('viewAny', App\Models\HistoriaClinica::class)
    <a data-bs-toggle="collapse" href="#historiasMenu" role="button">
        <i class="bi bi-file-medical-fill icon-historia"></i> Historias Clínicas
    </a>
    <div class="collapse {{ request()->routeIs('historias.*') ? 'show' : '' }}" id="historiasMenu">
        <a href="{{ route('historias.index') }}">• Listado</a>
        @can('create', App\Models\HistoriaClinica::class)
        <a href="{{ route('historias.create') }}">• Agregar</a>
        @endcan
    </div>
    @endcan

    {{-- ATENCIONES --}}
    @can('viewAny', App\Models\AtencionMedica::class)
    <a data-bs-toggle="collapse" href="#atencionesMenu" role="button">
        <i class="bi bi-heart-pulse-fill icon-atencion"></i> Atenciones
    </a>
    <div class="collapse {{ request()->routeIs('atenciones.*') ? 'show' : '' }}" id="atencionesMenu">
        <a href="{{ route('atenciones.index') }}">• Listado</a>
        @can('create', App\Models\AtencionMedica::class)
        <a href="{{ route('atenciones.create') }}">• Agregar</a>
        @endcan
    </div>
    @endcan

    {{-- REPORTES: visible para todos los autenticados, sin Policy propia --}}
    <a href="{{ route('reportes.index') }}">
        <i class="bi bi-bar-chart-fill icon-reporte"></i> Reportes
    </a>

    <div class="logout-container">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-link w-100 border-0 text-start">
                <i class="bi bi-box-arrow-right"></i> Cerrar sesión
            </button>
        </form>
    </div>

</div>