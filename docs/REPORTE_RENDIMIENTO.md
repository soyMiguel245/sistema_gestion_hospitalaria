# Reporte de rendimiento

**Fecha:** 06/08/2026 02:24
**Volumen de prueba:** 500 pacientes, 1000 atenciones médicas
**Repeticiones por medición:** 5

| Operación | Promedio | Mínimo | Máximo |
|---|---|---|---|
| Listado de pacientes (index) | 8.54 ms | 3.82 ms | 25.99 ms |
| Listado de atenciones con relaciones (paciente + medico) | 13.54 ms | 10.7 ms | 22.77 ms |
| Dashboard principal (4 conteos) | 21.04 ms | 4.47 ms | 86.36 ms |
| Dashboard de reportes (diagnosticos + ingresos agregados) | 18.87 ms | 3.71 ms | 77.6 ms |
| Busqueda de paciente por DNI (validacion de unicidad) | 1.78 ms | 0.39 ms | 6.98 ms |
