# Cifrado AES-256 de Historias Clínicas

**Fecha:** 2026-08-06
**Mecanismo:** Eloquent encrypted cast (Laravel) — AES-256-CBC vía `APP_KEY`
**Alcance:** Campos de texto libre clínico en `atenciones_medicas` y `diagnosticos`

## Objetivo

Proteger en reposo el contenido clínico sensible (motivo de consulta, diagnóstico, tratamiento, indicaciones, observaciones) frente a acceso directo a la base de datos.

## Campos cifrados

| Tabla | Campos cifrados | Campos sin cifrar (y por qué) |
|---|---|---|
| `atenciones_medicas` | `motivo_consulta`, `diagnostico`, `tratamiento`, `procedimientos`, `indicaciones`, `observaciones` | `cie10`, `tipo_paciente`, signos vitales, costos, estados — se usan en filtros y reportes, no son texto clínico libre |
| `diagnosticos` | `descripcion`, `observaciones` | `cie10`, `tipo` — código estándar y enum, no texto libre |

Mismo mecanismo ya usado en `User::two_factor_secret`: cast nativo `encrypted` de Eloquent, sin librerías adicionales.

## Migración de datos existentes

Comando propio `php artisan clinico:cifrar` (con `--dry-run`), idempotente: por cada fila intenta desencriptar el valor crudo con `Crypt::decryptString()`; si falla, lo trata como texto plano y lo cifra con `Crypt::encryptString()`.

| Tabla | Filas afectadas | Campos cifrados | Ya estaban cifrados |
|---|---|---|---|
| `atenciones_medicas` | 1 | 6 | 0 |
| `diagnosticos` | 0 | 0 | 0 |

`diagnosticos` estaba vacía al momento de la migración. Cualquier registro creado de ahora en adelante se cifra automáticamente por el cast del modelo.

## Verificación

- **Nivel aplicación** (Eloquent, cast activo): `AtencionMedica::first()->diagnostico` devuelve el texto legible `"INFLAMACION"`.
- **Nivel base de datos** (consulta cruda, sin cast): `DB::table('atenciones_medicas')->first()->diagnostico` devuelve el payload cifrado (JSON con `iv`, `value`, `mac`, `tag`, en base64), confirmando que el texto plano ya no existe en la columna.

## Efecto colateral encontrado y corregido: filtro de reportes

Cifrar una columna cambia su valor crudo en la base de datos, por lo que cualquier filtro SQL que compare ese contenido directamente deja de tener sentido. Una revisión de los controladores (`Select-String` sobre `where|LIKE|whereLike` cruzado con los nombres de los campos cifrados) encontró dos lugares en `ReporteController.php` (métodos `procedimientos()` y `exportarProcedimientosCsv()`) con `->where('procedimientos', '!=', '')`: ese filtro comparaba contra el valor crudo cifrado, que nunca es `''`, así que dejó de excluir atenciones sin procedimiento real — sin lanzar ningún error, solo devolviendo resultados incorrectos.

**Corrección:** se retiró ese filtro del SQL y se reemplazó por `->filter(fn ($a) => filled($a->procedimientos))` en PHP, aplicado después de traer los datos (momento en que Eloquent ya descifró el valor). `whereNotNull('procedimientos')` sí se mantuvo en SQL, porque el cast `encrypted` de Laravel no cifra valores `null`.

Esto confirma en la práctica el riesgo señalado antes de cifrar: cualquier filtro o búsqueda de texto sobre un campo cifrado falla en silencio, no con un error.

## Verificación de regresión

- Suite completa corrida tras el cifrado inicial: 79 tests, 209 assertions, sin fallos.
- Suite completa corrida de nuevo tras la corrección del filtro: 79 tests, 209 assertions, sin fallos — mismo conteo que antes, porque ningún test de la suite verifica por nombre este comportamiento específico. La corrección se validó por revisión manual del código, no por una aserción automatizada dedicada a este caso.

## Notas operativas

- `APP_KEY` es ahora indispensable para leer este contenido: si se pierde o se rota sin re-cifrar antes, los datos cifrados quedan permanentemente indescifrables. Debe resguardarse fuera del repositorio.
- El cifrado usa IV aleatorio por operación (mismo texto nunca produce el mismo resultado dos veces), por lo que no es posible hacer búsquedas `LIKE` directas en SQL sobre estos campos — como confirmó el caso de `procedimientos` arriba.
- La revisión de filtros sobre campos cifrados se hizo sobre `app\Http\Controllers\*.php`, sin `-Recurse`. Si existen controladores en subcarpetas u otra capa (Services, Livewire, Jobs) que consulte estos campos, conviene repetir la misma revisión ahí.