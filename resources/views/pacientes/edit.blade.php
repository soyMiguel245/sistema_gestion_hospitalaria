@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

    {{-- HEADER --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="fw-bold text-primary mb-1">
                <i class="bi bi-person-lines-fill"></i> Editar Paciente
            </h3>
            <p class="text-muted mb-0">
                Actualice la información clínica y administrativa del paciente
            </p>
        </div>

        <span class="badge bg-warning-subtle text-warning fs-6 px-3 py-2">
            ✏️ Actualización Clínica
        </span>
    </div>

    {{-- CARD FORM --}}
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-4">

            <form action="{{ route('pacientes.update', $paciente) }}" method="POST">
                @csrf
                @method('PUT')

                @include('pacientes.form', ['paciente' => $paciente])

                {{-- BOTONES --}}
                <div class="d-flex justify-content-between align-items-center mt-5 pt-3 border-top">
                    <a href="{{ route('pacientes.index') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-arrow-left-circle"></i> Cancelar
                    </a>

                    <button class="btn btn-primary btn-lg px-5 shadow">
                        <i class="bi bi-pencil-square"></i> Actualizar Paciente
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
