@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">➕ Nueva Especialidad</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('especialidades.store') }}">
                @csrf
                @include('especialidades._form')
                <div class="mt-4 text-end">
                    <a href="{{ route('especialidades.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button class="btn btn-primary px-4">Guardar Especialidad</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection