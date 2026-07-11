<!-- resources/views/reportes/_form.blade.php -->
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="reporte" class="form-label">Nombre del Reporte</label>
        <input type="text" name="reporte" id="reporte" class="form-control" 
               value="{{ old('reporte', $reporte->reporte ?? '') }}" required>
    </div>

    <div class="col-md-6 mb-3">
        <label for="tipo" class="form-label">Tipo de Reporte</label>
        <select name="tipo" id="tipo" class="form-control" required>
            <option value="">-- Seleccione --</option>
            <option value="PDF" {{ (old('tipo', $reporte->tipo ?? '')=='PDF') ? 'selected' : '' }}>PDF</option>
            <option value="Excel" {{ (old('tipo', $reporte->tipo ?? '')=='Excel') ? 'selected' : '' }}>Excel</option>
        </select>
    </div>

    <div class="col-md-12 mb-3">
        <label for="descripcion" class="form-label">Descripción (opcional)</label>
        <textarea name="descripcion" id="descripcion" class="form-control">{{ old('descripcion', $reporte->descripcion ?? '') }}</textarea>
    </div>
</div>
