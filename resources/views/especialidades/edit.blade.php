@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-warning text-white">
            <h4 class="mb-0">✏️ Editar Especialidad</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('especialidades.update', $especialidad) }}">
                @csrf
                @method('PUT')
                @include('especialidades._form')
                <div class="mt-4 text-end">
                    <a href="{{ route('especialidades.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button class="btn btn-warning px-4">Actualizar Especialidad</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection