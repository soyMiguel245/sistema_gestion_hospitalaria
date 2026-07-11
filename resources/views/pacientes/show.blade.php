@extends('layouts.app')

@section('content')
<div class="container">

    {{-- ================== ENCABEZADO ================== --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-primary">
                <i class="bi bi-clipboard2-pulse-fill"></i> Ficha Clínica del Paciente
            </h3>
            <p class="text-muted mb-0">
                Información médica y administrativa registrada
            </p>
        </div>

        <div>
            <a href="{{ route('pacientes.edit', $paciente->id) }}" class="btn btn-warning me-2">
                <i class="bi bi-pencil-square"></i> Editar
            </a>
            <a href="{{ route('pacientes.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left-circle"></i> Volver
            </a>
        </div>
    </div>

    {{-- ================== DATOS PERSONALES ================== --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-primary text-white fw-bold">
            <i class="bi bi-person-badge-fill"></i> Datos Personales
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><strong>DNI:</strong> {{ $paciente->dni }}</div>
                <div class="col-md-3"><strong>Historia Clínica:</strong> {{ $paciente->numero_historia_clinica }}</div>
                <div class="col-md-3"><strong>Fecha Nac.:</strong> {{ $paciente->fecha_nacimiento }}</div>
                <div class="col-md-3"><strong>Sexo:</strong> {{ $paciente->sexo }}</div>

                <div class="col-md-6 mt-2"><strong>Nombres:</strong> {{ $paciente->nombres }}</div>
                <div class="col-md-6 mt-2"><strong>Apellidos:</strong> {{ $paciente->apellidos }}</div>

                <div class="col-md-4 mt-2"><strong>Estado Civil:</strong> {{ $paciente->estado_civil }}</div>
                <div class="col-md-4 mt-2"><strong>Nacionalidad:</strong> {{ $paciente->nacionalidad }}</div>
                <div class="col-md-4 mt-2">
                    <strong>Estado:</strong>
                    <span class="badge {{ $paciente->estado == 'Activo' ? 'bg-success' : 'bg-secondary' }}">
                        {{ $paciente->estado }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ================== CONTACTO ================== --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-success text-white fw-bold">
            <i class="bi bi-telephone-fill"></i> Información de Contacto
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4"><strong>Teléfono:</strong> {{ $paciente->telefono }}</div>
                <div class="col-md-4"><strong>Email:</strong> {{ $paciente->correo }}</div>
                <div class="col-md-4"><strong>Dirección:</strong> {{ $paciente->direccion }}</div>
            </div>
        </div>
    </div>

    {{-- ================== CONTACTO DE EMERGENCIA ================== --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-danger text-white fw-bold">
            <i class="bi bi-exclamation-triangle-fill"></i> Contacto de Emergencia
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>Contacto:</strong> {{ $paciente->contacto_emergencia }}
                </div>
                <div class="col-md-6">
                    <strong>Teléfono:</strong> {{ $paciente->telefono_emergencia }}
                </div>
            </div>
        </div>
    </div>

    {{-- ================== DATOS CLÍNICOS ================== --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-info text-white fw-bold">
            <i class="bi bi-heart-pulse-fill"></i> Datos Clínicos
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><strong>Tipo de Sangre:</strong> {{ $paciente->tipo_sangre }}</div>
                <div class="col-md-4"><strong>Alergias:</strong> {{ $paciente->alergias }}</div>
                <div class="col-md-5"><strong>Enfermedades Crónicas:</strong> {{ $paciente->enfermedades_cronicas }}</div>

                <div class="col-md-12 mt-3">
                    <strong>Observaciones Médicas:</strong>
                    <div class="border rounded p-2 bg-light mt-1">
                        {{ $paciente->observaciones }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================== ADMINISTRATIVO ================== --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-secondary text-white fw-bold">
            <i class="bi bi-folder-fill"></i> Información Administrativa
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4"><strong>Seguro:</strong> {{ $paciente->tipo_seguro }}</div>
                <div class="col-md-4"><strong>Fecha Registro:</strong> {{ $paciente->fecha_registro }}</div>
            </div>
        </div>
    </div>

</div>
@endsection
