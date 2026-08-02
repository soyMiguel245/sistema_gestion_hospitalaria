# Matriz de Trazabilidad — Requerimientos vs. Implementación Real
### Sistema de Gestión Hospitalaria

Basada en `laboratorio 07.docx` (RF-01 a RF-05, RNF-01 a RNF-06). Cada fila
compara lo documentado formalmente contra el estado real y verificable del
código al día de hoy, sin optimismo — lo que no está hecho, se marca como
no hecho.

**Leyenda:** ✅ Implementado y verificado · ⚠️ Parcial · ❌ No implementado

---

## Requerimientos Funcionales

### RF-01 — Registro de Pacientes
> *"Validar que el DNI sea único y obligatorio, incluyendo datos personales,
> contacto, antecedentes y tipo de seguro."*

**Estado: ✅ Implementado y verificado**
- `PacienteController::store()` valida `unique:pacientes,dni`
- Cubre todos los campos del documento: contacto, antecedentes, tipo de seguro
- **Test automatizado:** `ReglasDeNegocioTest::no_se_puede_registrar_dos_pacientes_con_el_mismo_dni`

### RF-02 — Programación de Citas Médicas
> *"Verificando en tiempo real la disponibilidad de médicos... la cita será
> creada únicamente si el médico y el horario están disponibles."*

**Estado: ✅ Implementado y verificado**
- Regla `MedicoDisponible` valida solapamiento real de horario (no solo
  coincidencia exacta), considerando la duración de cada cita
- **Tests automatizados:** los 2 casos de `ReglasDeNegocioTest` sobre choque
  de horario (rechazo y aceptación correcta)
- *Nota:* la verificación de disponibilidad de **consultorio** mencionada en
  el documento no está implementada — solo se valida disponibilidad del médico

### RF-03 — Gestión de Historias Clínicas Electrónicas
> *"Registrar diagnósticos, prescripciones y observaciones... solo personal
> médico autenticado podrá acceder y modificar."*

**Estado: ⚠️ Parcial**
- ✅ El historial clínico (derivado de `AtencionMedica`) sí registra
  diagnóstico, tratamiento, observaciones
- ✅ Control de acceso implementado — pero **más granular** que lo
  documentado: no es solo "personal médico", son 4 roles diferenciados
  (administrador, médico y enfermera pueden ver; recepción no)
- ❌ El módulo `Diagnostico` (para registrar diagnósticos codificados CIE-10
  por separado) tiene el controlador y modelo listos, con Policy, pero
  **sin rutas activas** — no es una funcionalidad usable todavía, solo
  código preparado
- ✅ Auditoría de acceso (lectura) verificada con test automatizado

### RF-04 — Administración de Personal Médico
> *"Registrar, actualizar y consultar la información de médicos,
> especialidades y horarios laborales."*

**Estado: ✅ Implementado**
- `MedicoController` y `EspecialidadController` con CRUD completo y
  `MedicoPolicy`/`EspecialidadPolicy` protegiendo las rutas
- ❌ "Horarios laborales" específicamente no tiene un módulo dedicado —
  se gestiona indirectamente a través de la disponibilidad en citas

### RF-05 — Control de Inventario Farmacéutico
> *"Registrar ingreso, egreso y vencimiento de medicamentos, generando
> alertas ante niveles críticos de stock."*

**Estado: ❌ No implementado**
- Existía un `InventarioController` con todos los métodos vacíos (scaffold
  sin lógica), sin modelo ni vistas asociadas
- **Decisión tomada durante la auditoría:** se eliminó el controlador vacío
  en vez de dejarlo a medias, ya que no tenía ninguna funcionalidad real y
  generaba falsa sensación de módulo "en progreso"
- **Pendiente para una futura iteración** si este módulo se retoma

---

## Requerimientos No Funcionales

### RNF-01 — Seguridad y Confidencialidad
> *"Cifrado AES-256... contraseñas bajo hash SHA-512... acceso controlado
> por roles... cada acceso o modificación queda registrado en un log seguro."*

**Estado: ⚠️ Parcial — con desviaciones justificadas técnicamente**
- ✅ Control de acceso por roles: implementado y probado (44 combinaciones
  de rol × ruta verificadas automáticamente)
- ✅ Auditoría de accesos: `spatie/laravel-activitylog` + registro manual
  de lecturas de historial clínico, ambos verificados
- ✅ **2FA implementado** (mejora no solicitada en el documento original,
  pero refuerza este mismo requerimiento)
- ⚠️ Hash de contraseñas: se usa **bcrypt**, no SHA-512 como pide el
  documento. Esto es una **desviación intencional y técnicamente superior**
  — SHA-512 es un hash rápido, vulnerable a ataques de fuerza bruta con
  GPU; bcrypt está diseñado específicamente para contraseñas (lento a
  propósito). Se documenta como mejora, no como incumplimiento
- ❌ Cifrado AES-256 de historias clínicas: **no implementado**. Los datos
  están protegidos por control de acceso (RBAC) pero no cifrados a nivel
  de columna en la base de datos

### RNF-02 — Rendimiento del Sistema
> *"Operaciones críticas en máximo 2 segundos bajo carga normal (10 usuarios)."*

**Estado: ❌ No verificado**
- No se realizaron pruebas de carga (JMeter, k6, Artillery, etc.)
- Los tests automatizados miden tiempo de ejecución individual (visible en
  los logs de `php artisan test`), pero no simulan carga concurrente real

### RNF-03 — Usabilidad
> *"Completar tareas en máximo tres clics... 95% de usuarios sin asistencia."*

**Estado: ❌ No medido**
- No se realizaron pruebas de usabilidad con usuarios reales
- El diseño del sidebar sí agrupa funciones de forma que la mayoría de
  tareas comunes están a 2-3 clics, pero esto no fue medido formalmente

### RNF-04 — Disponibilidad y Respaldo
> *"99.5% de disponibilidad mensual... backups automáticos diarios...
> restauración en menos de 15 minutos."*

**Estado: ❌ No implementado**
- No existe automatización de backups (ni comando Artisan programado, ni
  tarea de Windows/cron configurada)
- No aplica medir disponibilidad sin un entorno de producción real desplegado

### RNF-05 — Escalabilidad
> *"Permitir adición de nuevos módulos sin afectar la estructura actual."*

**Estado: ✅ Demostrado con evidencia real**
- El sistema de roles pasó de un string simple a una tabla relacional
  (agregando el rol `enfermera`) **sin romper ningún test existente**
- Se agregó la tabla `archivos_medicos` normalizada sin afectar los
  módulos de Paciente, Cita o Especialidad
- Esta es la evidencia más fuerte de escalabilidad real: cambios de
  arquitectura ejecutados sin regresiones, confirmado por los 31 tests
  automatizados pasando después de cada cambio

### RNF-06 — Operatividad Offline
> *"Funcionar hasta 12 horas sin conexión, sincronizando al reconectar."*

**Estado: ❌ Fuera de alcance — documentado explícitamente**
- Ver `docs/DECISION_2FA.md` y las decisiones de alcance del informe
  técnico: la arquitectura Blade tradicional del proyecto es
  incompatible con este requerimiento sin una reescritura mayor
  (requeriría una SPA con IndexedDB/Service Workers, o una app híbrida)

---

## Resumen cuantitativo

| Estado | RF | RNF | Total |
|---|:-:|:-:|:-:|
| ✅ Implementado | 3 | 1 | 4 / 11 |
| ⚠️ Parcial | 1 | 1 | 2 / 11 |
| ❌ No implementado | 1 | 4 | 5 / 11 |

**Nota de honestidad académica:** esta matriz se presenta con los resultados
reales, incluyendo los requerimientos no cumplidos, porque una matriz de
trazabilidad que solo muestra logros no cumple su propósito real — su valor
está en identificar brechas con precisión, no en maximizar un porcentaje.
Los requerimientos marcados ❌ son candidatos claros para una segunda
iteración del proyecto, con tiempo dedicado específicamente a rendimiento,
respaldo automatizado y el módulo de inventario.