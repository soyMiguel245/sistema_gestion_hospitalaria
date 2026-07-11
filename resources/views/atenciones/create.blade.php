@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow border-0">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">➕ Nueva Atención Médica</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('atenciones.store') }}" enctype="multipart/form-data">
                @csrf
                @include('atenciones._form')
                <div class="mt-4 text-end">
                    <a href="{{ route('atenciones.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button class="btn btn-success px-4">Guardar Atención</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
