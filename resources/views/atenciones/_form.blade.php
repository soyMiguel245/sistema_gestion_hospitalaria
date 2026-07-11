@php 
    $atencion ??= null;
@endphp

{{-- ================== PESTAÑAS ================== --}}
<ul class="nav nav-tabs mb-4 fw-semibold border-bottom-0">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#general">🩺 General</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#signos">❤️ Signos Vitales</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#procedimientos">🧪 Diagnóstico</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#facturacion">💰 Facturación</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#archivos">📎 Archivos</a>
    </li>
</ul>

<div class="tab-content">

{{-- ================= GENERAL ================= --}}
<div class="tab-pane fade show active" id="general">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-primary text-white fw-bold">
            🩺 Información General del Paciente
        </div>
        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label fw-semibold">👤 Paciente *</label>
                    <select name="paciente_id" class="form-select" required>
                        <option value="">Seleccione</option>
                        @foreach($pacientes as $p)
                            <option value="{{ $p->id }}" {{ old('paciente_id',$atencion->paciente_id ?? '')==$p->id?'selected':'' }}>
                                {{ $p->nombres }} {{ $p->apellidos }}
                                @if($p->alergias) ⚠️ Alergias @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                   <label class="form-label fw-semibold">👨‍⚕️ Médico Responsable *</label>
                   <select name="medico_id" class="form-select" required>
                       <option value="">Seleccione</option>
                       @foreach($medicos as $m)
                           <option value="{{ $m->id }}" {{ old('medico_id',$atencion->medico_id ?? '')==$m->id?'selected':'' }}>
                               {{ $m->nombres }} {{ $m->apellidos }}
                           </option>
                       @endforeach
                   </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-semibold">📝 Motivo de Consulta *</label>
                    <textarea name="motivo_consulta" class="form-control" rows="3" required>{{ old('motivo_consulta',$atencion->motivo_consulta ?? '') }}</textarea>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-semibold">📌 Observaciones Clínicas</label>
                    <textarea name="observaciones" class="form-control" rows="2">{{ old('observaciones',$atencion->observaciones ?? '') }}</textarea>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- ================= SIGNOS VITALES ================= --}}
<div class="tab-pane fade" id="signos">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-danger text-white fw-bold">
            ❤️ Signos Vitales
        </div>
        <div class="card-body">
            <div class="row g-3">
                @foreach([
                    'presion_arterial'=>'🩸 Presión',
                    'frecuencia_cardiaca'=>'❤️ FC',
                    'frecuencia_respiratoria'=>'🌬 FR',
                    'temperatura'=>'🌡 Temp °C',
                    'saturacion_o2'=>'🫁 SpO₂',
                    'peso'=>'⚖ Peso kg',
                    'talla'=>'📏 Talla m',
                    'imc'=>'📊 IMC'
                ] as $name=>$label)
                <div class="col-md-3">
                    <label class="form-label fw-semibold">{{ $label }}</label>
                    <input type="text" name="{{ $name }}" class="form-control"
                           value="{{ old($name,$atencion->$name ?? '') }}">
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ================= DIAGNÓSTICO ================= --}}
<div class="tab-pane fade" id="procedimientos">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-success text-white fw-bold">
            🧪 Diagnóstico y Tratamiento
        </div>
        <div class="card-body">
            <div class="row g-3">
                @foreach([
                    'diagnostico'=>'🧠 Diagnóstico',
                    'tratamiento'=>'💊 Tratamiento',
                    'procedimientos'=>'🔬 Procedimientos',
                    'indicaciones'=>'📋 Indicaciones'
                ] as $f=>$l)
                <div class="col-md-6">
                    <label class="form-label fw-semibold">{{ $l }}</label>
                    <textarea name="{{ $f }}" class="form-control" rows="3">{{ old($f,$atencion->$f ?? '') }}</textarea>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ================= FACTURACIÓN ================= --}}
<div class="tab-pane fade" id="facturacion">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-warning fw-bold">
            💰 Información Administrativa
        </div>
        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label fw-semibold">💵 Costo</label>
                    <input type="number" step="0.01" name="costo" class="form-control"
                           value="{{ old('costo',$atencion->costo ?? '') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">📄 Estado de Pago</label>
                    <select name="estado_pago" class="form-select">
                        @foreach(['Pendiente','Pagado','Exonerado'] as $e)
                            <option value="{{ $e }}" {{ old('estado_pago',$atencion->estado_pago ?? '')==$e?'selected':'' }}>{{ $e }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">📌 Estado Atención</label>
                    <select name="estado" class="form-select">
                        @foreach(['Pendiente','En Progreso','Atendido','Derivado','Alta'] as $e)
                            <option value="{{ $e }}" {{ old('estado',$atencion->estado ?? '')==$e?'selected':'' }}>{{ $e }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 mt-3">
                    <label class="form-label fw-semibold">🏥 Tipo de Paciente *</label>
                    <select name="tipo_paciente" class="form-select" required>
                        <option value="">Seleccione</option>
                        @foreach(['Particular','Seguro','Convenio'] as $tipo)
                            <option value="{{ $tipo }}" {{ old('tipo_paciente', $atencion->tipo_paciente ?? '')==$tipo?'selected':'' }}>
                                {{ $tipo }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- ================= ARCHIVOS ================= --}}
<div class="tab-pane fade" id="archivos">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-secondary text-white fw-bold">
            📎 Documentos Clínicos
        </div>
        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label fw-semibold">🧾 Exámenes Adjuntos</label>
                    <input type="file" name="examenes_adjuntos[]" class="form-control" multiple>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">🖼 Imágenes Médicas</label>
                    <input type="file" name="imagenes_medicas[]" class="form-control" multiple>
                </div>

            </div>
        </div>
    </div>
</div>
