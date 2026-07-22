@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow border-0">
        <div class="card-header bg-info text-white">
            <h4>🧾 Ficha Clínica – Atención #{{ $atencion->id }}</h4>
        </div>
        <div class="card-body">

            <h5 class="text-primary">👤 Paciente</h5>
            <p>{{ $atencion->paciente->nombres ?? '-' }} {{ $atencion->paciente->apellidos ?? '' }}</p>

            <h5 class="text-success mt-3">👨‍⚕️ Médico Responsable</h5>
            <p>{{ $atencion->medico->nombres ?? '-' }} {{ $atencion->medico->apellidos ?? '' }}</p>

            <h5 class="text-danger mt-3">🩺 Diagnóstico</h5>
            <p>{{ $atencion->diagnostico ?? '-' }}</p>

            <h5 class="text-success mt-3">💊 Tratamiento</h5>
            <p>{{ $atencion->tratamiento ?? '-' }}</p>

            <h5 class="mt-3">📌 Estado</h5>
            <span class="badge {{ $atencion->estado == 'Atendido' ? 'bg-success' : 'bg-warning text-dark' }}">
                {{ $atencion->estado }}
            </span>

            {{-- ================= ARCHIVOS ADJUNTOS ================= --}}
            <h5 class="text-secondary mt-4">📎 Archivos Adjuntos</h5>

            @php
                $examenes = $atencion->archivos->where('tipo', 'examen');
                $imagenes = $atencion->archivos->where('tipo', 'imagen');
            @endphp

            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-semibold">🧾 Exámenes</h6>
                    @forelse($examenes as $archivo)
                        <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2">
                            <a href="{{ $archivo->url }}" target="_blank" class="text-decoration-none">
                                📄 {{ $archivo->nombre_original }}
                            </a>
                            <form action="{{ route('archivos.destroy', $archivo) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('¿Eliminar este archivo?')"
                                        title="Eliminar archivo">
                                    🗑
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-muted small">No hay exámenes adjuntos.</p>
                    @endforelse
                </div>

                <div class="col-md-6">
                    <h6 class="fw-semibold">🖼 Imágenes Médicas</h6>
                    @forelse($imagenes as $archivo)
                        <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2">
                            <a href="{{ $archivo->url }}" target="_blank" class="text-decoration-none">
                                🖼 {{ $archivo->nombre_original }}
                            </a>
                            <form action="{{ route('archivos.destroy', $archivo) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('¿Eliminar este archivo?')"
                                        title="Eliminar archivo">
                                    🗑
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-muted small">No hay imágenes médicas adjuntas.</p>
                    @endforelse
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('atenciones.index') }}" class="btn btn-secondary">⬅ Volver</a>
            </div>

        </div>
    </div>
</div>
@endsection