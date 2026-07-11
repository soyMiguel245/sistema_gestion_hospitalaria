<div class="mb-3">
    <label>Nombre</label>
    <input type="text" name="nombre" class="form-control"
           value="{{ old('nombre', $especialidad->nombre ?? '') }}" required>
</div>

<div class="mb-3">
    <label>Descripción</label>
    <textarea name="descripcion" class="form-control">{{ old('descripcion', $especialidad->descripcion ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label>Estado</label>
    <select name="estado" class="form-control">
        <option value="1" {{ (old('estado', $especialidad->estado ?? 1) == 1) ? 'selected' : '' }}>Activa</option>
        <option value="0" {{ (old('estado', $especialidad->estado ?? 1) == 0) ? 'selected' : '' }}>Inactiva</option>
    </select>
</div>
