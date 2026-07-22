<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

<style>
:root {
    --cl-navy: #14344F;
    --cl-teal:rgb(5, 143, 12);
    --cl-teal-soft: #EAF6F6;
    --cl-bg: #F7F9FA;
    --cl-card: #FFFFFF;
    --cl-border:rgb(168, 174, 174);
    --cl-text: #1C2B36;
    --cl-muted:rgb(9, 10, 9);
    --cl-amber: #C97B2E;
    --cl-amber-soft: #FCF3E7;
    --cl-red: #C1484A;
    --cl-red-soft: #FCEEEE;
    --cl-blue:rgb(58, 208, 198);
    --cl-blue-soft: #EAF3FA;
    --cl-green:rgb(17, 183, 108);
    --cl-green-soft: #EAF7F1;
}

.clinical-form { font-family: 'Inter', -apple-system, sans-serif; color: var(--cl-text); }

.clinical-jumpnav {
    display: flex;
    flex-wrap: wrap;
    gap: .4rem;
    background: var(--cl-bg);
    border: 1px solid var(--cl-border);
    border-radius: 10px;
    padding: 6px;
    margin-bottom: 1.5rem;
    position: sticky;
    top: 8px;
    z-index: 5;
}
.clinical-jumpnav a {
    display: flex;
    align-items: center;
    gap: .4rem;
    padding: .5rem .9rem;
    border-radius: 7px;
    font-size: .82rem;
    font-weight: 600;
    color: var(--cl-muted);
    text-decoration: none;
    transition: background .15s ease, color .15s ease;
}
.clinical-jumpnav a:hover { background: var(--cl-teal-soft); color: var(--cl-teal); }

.clinical-section { scroll-margin-top: 70px; margin-bottom: 1.5rem; }
.clinical-card {
    background: var(--cl-card);
    border: 1px solid var(--cl-border);
    border-radius: 12px;
    overflow: hidden;
    transition: box-shadow .2s ease;
}
.clinical-card:hover { box-shadow: 0 4px 18px rgba(20,52,79,.06); }
.clinical-card-header {
    display: flex;
    align-items: center;
    gap: .55rem;
    padding: .9rem 1.15rem;
    background: var(--cl-teal-soft);
    color: var(--cl-navy);
    font-weight: 700;
    font-size: .92rem;
    border-left: 4px solid var(--cl-teal);
}
.clinical-card-body { padding: 1.25rem; }

.clinical-form label { font-size: .82rem; font-weight: 600; color: var(--cl-navy); letter-spacing: .01em; margin-bottom: .35rem; display: flex; align-items: center; gap: .4rem; }
.clinical-form label .req { color: var(--cl-red); font-weight: 700; }
.clinical-form .form-control,
.clinical-form .form-select {
    border: 1px solid var(--cl-border);
    border-radius: 7px;
    padding: .55rem .75rem;
    font-size: .92rem;
    color: var(--cl-text);
    background-color: #fff;
    transition: border-color .15s ease, box-shadow .15s ease;
}
.clinical-form .form-control:focus,
.clinical-form .form-select:focus {
    border-color: var(--cl-teal);
    box-shadow: 0 0 0 3px var(--cl-teal-soft);
}

#alergiaBanner {
    display: none;
    align-items: center;
    gap: .6rem;
    background: var(--cl-red-soft);
    border: 1px solidrgb(58, 177, 58);
    color: var(--cl-red);
    border-radius: 9px;
    padding: .75rem 1rem;
    font-size: .87rem;
    font-weight: 600;
    margin-bottom: 1.25rem;
}

.monitor-panel {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: .9rem;
}
.monitor-cell {
    border-radius: 9px;
    padding: .9rem 1rem;
    display: flex;
    flex-direction: column;
    gap: .3rem;
    border: 1px solid var(--cl-border);
    border-left: 3px solid transparent;
    background: var(--cl-bg);
    transition: transform .15s ease, box-shadow .15s ease;
}
.monitor-cell:focus-within {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(20,52,79,.08);
}
.monitor-cell .m-label {
    font-size: .68rem;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: var(--cl-muted);
    font-weight: 700;
}
.monitor-cell input {
    font-family: 'JetBrains Mono', monospace;
    font-weight: 700;
    font-size: 1.3rem;
    background: transparent;
    border: none;
    padding: .1rem 0;
    width: 100%;
    color: var(--cl-navy);
}
.monitor-cell input:focus { outline: none; }
.monitor-cell .m-unit { font-size: .7rem; color: var(--cl-muted); font-weight: 500; }

.monitor-cell.m-red   { border-left-color: var(--cl-red); background: var(--cl-red-soft); }
.monitor-cell.m-red input { color: var(--cl-red); }
.monitor-cell.m-blue  { border-left-color: var(--cl-blue); background: var(--cl-blue-soft); }
.monitor-cell.m-blue input { color: var(--cl-blue); }
.monitor-cell.m-amber { border-left-color: var(--cl-amber); background: var(--cl-amber-soft); }
.monitor-cell.m-amber input { color: var(--cl-amber); }
.monitor-cell.m-green { border-left-color: var(--cl-green); background: var(--cl-green-soft); }
.monitor-cell.m-green input { color: var(--cl-green); }

.imc-auto {
    font-size: .7rem;
    color: var(--cl-teal);
    font-weight: 600;
    display: none;
}

@media (max-width: 992px) {
    .monitor-panel { grid-template-columns: repeat(2, 1fr); }
}
</style>

@php
    $atencion ??= null;
@endphp

<div class="clinical-form">

    <div id="alergiaBanner">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span id="alergiaTexto"></span>
    </div>

    <nav class="clinical-jumpnav">
        <a href="#sec-general"><i class="bi bi-clipboard2-pulse"></i> General</a>
        <a href="#sec-signos"><i class="bi bi-heart-pulse"></i> Signos Vitales</a>
        <a href="#sec-diagnostico"><i class="bi bi-file-medical"></i> Diagnóstico</a>
        <a href="#sec-facturacion"><i class="bi bi-receipt"></i> Facturación</a>
        <a href="#sec-archivos"><i class="bi bi-paperclip"></i> Archivos</a>
    </nav>

    <div class="clinical-section" id="sec-general">
        <div class="clinical-card">
            <div class="clinical-card-header">
                <i class="bi bi-person-badge"></i> Información General del Paciente
            </div>
            <div class="clinical-card-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label>Paciente <span class="req">*</span></label>
                        <select name="paciente_id" id="pacienteSelect" class="form-select" required>
                            <option value="">Seleccione</option>
                            @foreach($pacientes as $p)
                                <option value="{{ $p->id }}"
                                    data-alergias="{{ $p->alergias }}"
                                    {{ old('paciente_id',$atencion->paciente_id ?? '')==$p->id?'selected':'' }}>
                                    {{ $p->nombres }} {{ $p->apellidos }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Médico Responsable <span class="req">*</span></label>
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
                        <label>Motivo de Consulta <span class="req">*</span></label>
                        <textarea name="motivo_consulta" class="form-control" rows="3" required>{{ old('motivo_consulta',$atencion->motivo_consulta ?? '') }}</textarea>
                    </div>

                    <div class="col-md-12">
                        <label>Observaciones Clínicas</label>
                        <textarea name="observaciones" class="form-control" rows="2">{{ old('observaciones',$atencion->observaciones ?? '') }}</textarea>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="clinical-section" id="sec-signos">
        <div class="clinical-card">
            <div class="clinical-card-header">
                <i class="bi bi-activity"></i> Signos Vitales
            </div>
            <div class="clinical-card-body">
                <div class="monitor-panel">

                    <div class="monitor-cell m-blue">
                        <span class="m-label">Presión Arterial</span>
                        <input type="text" name="presion_arterial" placeholder="120/80"
                               value="{{ old('presion_arterial',$atencion->presion_arterial ?? '') }}">
                        <span class="m-unit">mmHg</span>
                    </div>

                    <div class="monitor-cell m-red">
                        <span class="m-label">Frec. Cardiaca</span>
                        <input type="number" step="1" name="frecuencia_cardiaca"
                               value="{{ old('frecuencia_cardiaca',$atencion->frecuencia_cardiaca ?? '') }}">
                        <span class="m-unit">lpm</span>
                    </div>

                    <div class="monitor-cell m-blue">
                        <span class="m-label">Frec. Respiratoria</span>
                        <input type="number" step="1" name="frecuencia_respiratoria"
                               value="{{ old('frecuencia_respiratoria',$atencion->frecuencia_respiratoria ?? '') }}">
                        <span class="m-unit">rpm</span>
                    </div>

                    <div class="monitor-cell m-amber">
                        <span class="m-label">Temperatura</span>
                        <input type="number" step="0.1" name="temperatura"
                               value="{{ old('temperatura',$atencion->temperatura ?? '') }}">
                        <span class="m-unit">°C</span>
                    </div>

                    <div class="monitor-cell m-green">
                        <span class="m-label">Saturación O₂</span>
                        <input type="number" step="1" name="saturacion_o2"
                               value="{{ old('saturacion_o2',$atencion->saturacion_o2 ?? '') }}">
                        <span class="m-unit">%</span>
                    </div>

                    <div class="monitor-cell m-green">
                        <span class="m-label">Peso</span>
                        <input type="number" step="0.01" id="pesoInput" name="peso"
                               value="{{ old('peso',$atencion->peso ?? '') }}">
                        <span class="m-unit">kg</span>
                    </div>

                    <div class="monitor-cell m-green">
                        <span class="m-label">Talla</span>
                        <input type="number" step="0.01" id="tallaInput" name="talla"
                               value="{{ old('talla',$atencion->talla ?? '') }}">
                        <span class="m-unit">m</span>
                    </div>

                    <div class="monitor-cell m-amber">
                        <span class="m-label">IMC</span>
                        <input type="number" step="0.01" id="imcInput" name="imc"
                               value="{{ old('imc',$atencion->imc ?? '') }}">
                        <span class="m-unit">kg/m² <span class="imc-auto" id="imcAuto">calculado automáticamente</span></span>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="clinical-section" id="sec-diagnostico">
        <div class="clinical-card">
            <div class="clinical-card-header">
                <i class="bi bi-file-medical"></i> Diagnóstico y Tratamiento
            </div>
            <div class="clinical-card-body">
                <div class="row g-3">
                    @foreach([
                        'diagnostico'=>['Diagnóstico','bi-clipboard2-check'],
                        'tratamiento'=>['Tratamiento','bi-capsule'],
                        'procedimientos'=>['Procedimientos','bi-scissors'],
                        'indicaciones'=>['Indicaciones','bi-list-check']
                    ] as $f=>$meta)
                    <div class="col-md-6">
                        <label><i class="bi {{ $meta[1] }}"></i> {{ $meta[0] }}</label>
                        <textarea name="{{ $f }}" class="form-control" rows="3">{{ old($f,$atencion->$f ?? '') }}</textarea>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="clinical-section" id="sec-facturacion">
        <div class="clinical-card">
            <div class="clinical-card-header">
                <i class="bi bi-receipt"></i> Información Administrativa
            </div>
            <div class="clinical-card-body">
                <div class="row g-3">

                    <div class="col-md-4">
                        <label>Costo</label>
                        <input type="number" step="0.01" name="costo" class="form-control"
                               value="{{ old('costo',$atencion->costo ?? '') }}">
                    </div>

                    <div class="col-md-4">
                        <label>Estado de Pago</label>
                        <select name="estado_pago" class="form-select">
                            @foreach(['Pendiente','Pagado','Exonerado'] as $e)
                                <option value="{{ $e }}" {{ old('estado_pago',$atencion->estado_pago ?? '')==$e?'selected':'' }}>{{ $e }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Estado Atención</label>
                        <select name="estado" class="form-select">
                            @foreach(['Pendiente','En Progreso','Atendido','Derivado','Alta'] as $e)
                                <option value="{{ $e }}" {{ old('estado',$atencion->estado ?? '')==$e?'selected':'' }}>{{ $e }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mt-3">
                        <label>Tipo de Paciente <span class="req">*</span></label>
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

    <div class="clinical-section" id="sec-archivos">
        <div class="clinical-card">
            <div class="clinical-card-header">
                <i class="bi bi-paperclip"></i> Documentos Clínicos
            </div>
            <div class="clinical-card-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label><i class="bi bi-file-earmark-pdf"></i> Exámenes Adjuntos</label>
                        <input type="file" name="examenes_adjuntos[]" class="form-control" multiple>
                    </div>

                    <div class="col-md-6">
                        <label><i class="bi bi-image"></i> Imágenes Médicas</label>
                        <input type="file" name="imagenes_medicas[]" class="form-control" multiple>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

<script>
(function () {
    var select = document.getElementById('pacienteSelect');
    var banner = document.getElementById('alergiaBanner');
    var texto = document.getElementById('alergiaTexto');

    function actualizarAlergia() {
        var opcion = select.options[select.selectedIndex];
        var alergias = opcion ? opcion.getAttribute('data-alergias') : '';
        if (alergias && alergias.trim() !== '') {
            texto.textContent = 'Este paciente tiene alergias registradas: ' + alergias;
            banner.style.display = 'flex';
        } else {
            banner.style.display = 'none';
        }
    }
    if (select) {
        select.addEventListener('change', actualizarAlergia);
        actualizarAlergia();
    }

    var peso = document.getElementById('pesoInput');
    var talla = document.getElementById('tallaInput');
    var imc = document.getElementById('imcInput');
    var imcAuto = document.getElementById('imcAuto');

    function calcularIMC() {
        var p = parseFloat(peso.value);
        var t = parseFloat(talla.value);
        if (p > 0 && t > 0) {
            imc.value = (p / (t * t)).toFixed(2);
            imcAuto.style.display = 'inline';
        }
    }
    if (peso && talla) {
        peso.addEventListener('input', calcularIMC);
        talla.addEventListener('input', calcularIMC);
    }

    document.querySelectorAll('.clinical-jumpnav a').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            var target = document.querySelector(this.getAttribute('href'));
            if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
})();
</script>