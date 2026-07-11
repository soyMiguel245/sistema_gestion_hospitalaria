{{-- ================== ESTILOS SOLO CABECERAS ================== --}}
<style>
    .section-header {
        padding: 10px 15px;
        border-radius: 6px;
        font-weight: 700;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .header-personal {
        background-color:rgb(217, 217, 242);
        color:rgb(37, 3, 208);
    }

    .header-contacto {
        background-color:rgb(193, 241, 206);
        color:rgb(2, 65, 30);
    }

    .header-emergencia {
        background-color:rgb(242, 192, 192);
        color:rgb(237, 11, 11);
    }

    .header-clinico {
        background-color:rgb(194, 242, 243);
        color:rgb(7, 184, 178);
    }

    .header-admin {
        background-color:rgb(167, 174, 244);
        color:rgb(4, 31, 237);
    }
</style>

{{-- ================== DATOS PERSONALES ================== --}}
<div class="mb-4 p-3 rounded bg-light">
    <h5 class="section-header header-personal">
        <i class="bi bi-person-badge-fill"></i> Datos Personales
    </h5>

    <div class="row">
        <div class="col-md-3">
            <label class="form-label fw-semibold">DNI</label>
            <input type="text" name="dni" class="form-control"
                   value="{{ old('dni', $paciente->dni ?? '') }}" required>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Nombres</label>
            <input type="text" name="nombres" class="form-control"
                   value="{{ old('nombres', $paciente->nombres ?? '') }}" required>
        </div>

        <div class="col-md-5">
            <label class="form-label fw-semibold">Apellidos</label>
            <input type="text" name="apellidos" class="form-control"
                   value="{{ old('apellidos', $paciente->apellidos ?? '') }}" required>
        </div>

        <div class="col-md-3 mt-3">
            <label class="form-label fw-semibold">Fecha Nacimiento</label>
            <input type="date" name="fecha_nacimiento" class="form-control"
                   value="{{ old('fecha_nacimiento', $paciente->fecha_nacimiento ?? '') }}">
        </div>

        <div class="col-md-3 mt-3">
            <label class="form-label fw-semibold">Sexo</label>
            <select name="sexo" class="form-select">
                <option value="">Seleccione</option>
                <option value="Masculino" {{ old('sexo', $paciente->sexo ?? '')=='Masculino'?'selected':'' }}>Masculino</option>
                <option value="Femenino" {{ old('sexo', $paciente->sexo ?? '')=='Femenino'?'selected':'' }}>Femenino</option>
                <option value="Otro" {{ old('sexo', $paciente->sexo ?? '')=='Otro'?'selected':'' }}>Otro</option>
            </select>
        </div>

        <div class="col-md-3 mt-3">
            <label class="form-label fw-semibold">Estado Civil</label>
            <input type="text" name="estado_civil" class="form-control"
                   value="{{ old('estado_civil', $paciente->estado_civil ?? '') }}">
        </div>

        <div class="col-md-3 mt-3">
            <label class="form-label fw-semibold">Nacionalidad</label>
            <input type="text" name="nacionalidad" class="form-control"
                   value="{{ old('nacionalidad', $paciente->nacionalidad ?? '') }}">
        </div>
    </div>
</div>

{{-- ================== CONTACTO ================== --}}
<div class="mb-4 p-3 rounded bg-light">
    <h5 class="section-header header-contacto">
        <i class="bi bi-telephone-fill"></i> Información de Contacto
    </h5>

    <div class="row">
        <div class="col-md-4">
            <label class="form-label fw-semibold">Teléfono</label>
            <input type="text" name="telefono" class="form-control"
                   value="{{ old('telefono', $paciente->telefono ?? '') }}">
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Correo Electrónico</label>
            <input type="email" name="correo" class="form-control"
                   value="{{ old('correo', $paciente->correo ?? '') }}">
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Dirección</label>
            <input type="text" name="direccion" class="form-control"
                   value="{{ old('direccion', $paciente->direccion ?? '') }}">
        </div>
    </div>
</div>

{{-- ================== EMERGENCIA ================== --}}
<div class="mb-4 p-3 rounded bg-light">
    <h5 class="section-header header-emergencia">
        <i class="bi bi-exclamation-triangle-fill"></i> Contacto de Emergencia
    </h5>

    <div class="row">
        <div class="col-md-6">
            <label class="form-label fw-semibold">Persona de Contacto</label>
            <input type="text" name="contacto_emergencia" class="form-control"
                   value="{{ old('contacto_emergencia', $paciente->contacto_emergencia ?? '') }}">
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Teléfono</label>
            <input type="text" name="telefono_emergencia" class="form-control"
                   value="{{ old('telefono_emergencia', $paciente->telefono_emergencia ?? '') }}">
        </div>
    </div>
</div>

{{-- ================== DATOS CLÍNICOS ================== --}}
<div class="mb-4 p-3 rounded bg-light">
    <h5 class="section-header header-clinico">
        <i class="bi bi-heart-pulse-fill"></i> Datos Clínicos
    </h5>

    <div class="row">
        <div class="col-md-3">
            <label class="form-label fw-semibold">Tipo de Sangre</label>
            <input type="text" name="tipo_sangre" class="form-control"
                   value="{{ old('tipo_sangre', $paciente->tipo_sangre ?? '') }}">
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Alergias</label>
            <input type="text" name="alergias" class="form-control"
                   value="{{ old('alergias', $paciente->alergias ?? '') }}">
        </div>

        <div class="col-md-5">
            <label class="form-label fw-semibold">Enfermedades Crónicas</label>
            <input type="text" name="enfermedades_cronicas" class="form-control"
                   value="{{ old('enfermedades_cronicas', $paciente->enfermedades_cronicas ?? '') }}">
        </div>

        <div class="col-md-12 mt-3">
            <label class="form-label fw-semibold">Observaciones Médicas</label>
            <textarea name="observaciones" rows="3" class="form-control">{{ old('observaciones', $paciente->observaciones ?? '') }}</textarea>
        </div>
    </div>
</div>

{{-- ================== ADMINISTRATIVO ================== --}}
<div class="mb-3 p-3 rounded bg-light">
    <h5 class="section-header header-admin">
        <i class="bi bi-folder-fill"></i> Información Administrativa
    </h5>

    <div class="row">
        <div class="col-md-4">
            <label class="form-label fw-semibold">Tipo de Seguro</label>
            <select name="tipo_seguro" class="form-select">
                <option value="">Seleccione</option>
                <option value="SIS" {{ old('tipo_seguro', $paciente->tipo_seguro ?? '')=='SIS'?'selected':'' }}>SIS</option>
                <option value="ESSALUD" {{ old('tipo_seguro', $paciente->tipo_seguro ?? '')=='ESSALUD'?'selected':'' }}>ESSALUD</option>
                <option value="Privado" {{ old('tipo_seguro', $paciente->tipo_seguro ?? '')=='Privado'?'selected':'' }}>Privado</option>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Estado</label>
            <select name="estado" class="form-select">
                <option value="Activo" {{ old('estado', $paciente->estado ?? 'Activo')=='Activo'?'selected':'' }}>Activo</option>
                <option value="Inactivo" {{ old('estado', $paciente->estado ?? '')=='Inactivo'?'selected':'' }}>Inactivo</option>
            </select>
        </div>
    </div>
</div>
