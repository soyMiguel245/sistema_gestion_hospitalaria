\# Cifrado AES-256 de Historias Clínicas



\*\*Fecha:\*\* 2026-08-06

\*\*Mecanismo:\*\* Eloquent encrypted cast (Laravel) — AES-256-CBC vía `APP\_KEY`

\*\*Alcance:\*\* Campos de texto libre clínico en `atenciones\_medicas` y `diagnosticos`



\## Objetivo



Proteger en reposo el contenido clínico sensible (motivo de consulta, diagnóstico, tratamiento, indicaciones, observaciones) frente a acceso directo a la base de datos.



\## Campos cifrados



| Tabla | Campos cifrados | Campos sin cifrar (y por qué) |

|---|---|---|

| `atenciones\_medicas` | `motivo\_consulta`, `diagnostico`, `tratamiento`, `procedimientos`, `indicaciones`, `observaciones` | `cie10`, `tipo\_paciente`, signos vitales, costos, estados — se usan en filtros y reportes, no son texto clínico libre |

| `diagnosticos` | `descripcion`, `observaciones` | `cie10`, `tipo` — código estándar y enum, no texto libre |



Mismo mecanismo ya usado en `User::two\_factor\_secret`: cast nativo `encrypted` de Eloquent, sin librerías adicionales.



\## Migración de datos existentes



Comando propio `php artisan clinico:cifrar` (con `--dry-run`), idempotente: por cada fila intenta desencriptar el valor crudo con `Crypt::decryptString()`; si falla, lo trata como texto plano y lo cifra con `Crypt::encryptString()`. Escribe directo con `DB::table()->update()`, sin pasar por el cast del modelo, para no cifrar dos veces.



\*\*Resultado de la ejecución (dry-run y real dieron el mismo resultado, confirmando idempotencia):\*\*



| Tabla | Filas afectadas | Campos cifrados | Ya estaban cifrados |

|---|---|---|---|

| `atenciones\_medicas` | 1 | 6 | 0 |

| `diagnosticos` | 0 | 0 | 0 |



`diagnosticos` estaba vacía al momento de la migración, por eso no hay campos migrados ahí. Cualquier diagnóstico creado de ahora en adelante se cifra automáticamente al guardarse por el cast del modelo.



\## Verificación



\- \*\*Nivel aplicación\*\* (Eloquent, cast activo): `AtencionMedica::first()->diagnostico` devuelve el texto legible `"INFLAMACION"`.

\- \*\*Nivel base de datos\*\* (consulta cruda, sin cast): `DB::table('atenciones\_medicas')->first()->diagnostico` devuelve un payload cifrado — JSON con `iv`, `value`, `mac` y `tag`, codificado en base64 — confirmando que el texto plano ya no existe en la columna.



\## Auditoría



`AtencionMedica` usa `Spatie\\Activitylog` para registrar cambios (`logOnlyDirty`). Los 6 campos cifrados se agregaron a `logExcept` para que el historial de auditoría no termine guardando el valor ya descifrado en texto plano dentro de `activity\_log.properties`.



\## Verificación de regresión



Suite completa de 79 tests (209 assertions) corrida tras el cambio: sin fallos.



\## Notas operativas



\- `APP\_KEY` es ahora indispensable para leer este contenido: si se pierde o se rota sin re-cifrar antes, los datos cifrados quedan permanentemente indescifrables. Debe resguardarse fuera del repositorio.

\- El cifrado usa IV aleatorio por operación (mismo texto nunca produce el mismo resultado dos veces), por lo que no es posible hacer búsquedas `LIKE` directas en SQL sobre estos campos.

