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
    {{-- HISTORIAS CLÍNICAS: solo médico/administrador/enfermera la van a ver.
     Ya no hay Policy de modelo (HistoriaClinica no existe), así que
     el chequeo es directo por rol, igual que en el controlador. --}}
    @if(auth()->user()->hasRole(['administrador', 'medico', 'enfermera']))
    <a href="{{ route('historias.index') }}">
        <i class="bi bi-file-medical-fill icon-historia"></i> Historias Clínicas
    </a>
    @endif
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
    {{-- MÉDICOS --}}
    @can('viewAny', App\Models\Medico::class)
    <a data-bs-toggle="collapse" href="#medicosMenu" role="button">
        <i class="bi bi-person-badge-fill icon-medico"></i> Médicos
    </a>
    <div class="collapse {{ request()->routeIs('medicos.*') ? 'show' : '' }}" id="medicosMenu">
        <a href="{{ route('medicos.index') }}">• Listado</a>
        @can('create', App\Models\Medico::class)
        <a href="{{ route('medicos.create') }}">• Agregar</a>
        @endcan
    </div>
    @endcan
    {{-- ESPECIALIDADES --}}
    @can('viewAny', App\Models\Especialidad::class)
    <a data-bs-toggle="collapse" href="#especialidadesMenu" role="button">
        <i class="bi bi-clipboard2-pulse-fill icon-especialidad"></i> Especialidades
    </a>
    <div class="collapse {{ request()->routeIs('especialidades.*') ? 'show' : '' }}" id="especialidadesMenu">
        <a href="{{ route('especialidades.index') }}">• Listado</a>
        @can('create', App\Models\Especialidad::class)
        <a href="{{ route('especialidades.create') }}">• Agregar</a>
        @endcan
    </div>
    @endcan
    {{-- REPORTES: mismo criterio de acceso que ReporteController (admin/medico) --}}
    @if(auth()->user()->hasRole(['administrador', 'medico']))
    <a href="{{ route('reportes.index') }}">
        <i class="bi bi-bar-chart-fill icon-reporte"></i> Reportes
    </a>
    @endif
    <div class="logout-container">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-link w-100 border-0 text-start">
                <i class="bi bi-box-arrow-right"></i> Cerrar sesión
            </button>
        </form>
    </div>
</div>