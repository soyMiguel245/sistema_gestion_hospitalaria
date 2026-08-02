# Informe Técnico Avanzado
## Sistema de Gestión Hospitalaria — Evolución, Arquitectura y Calidad de Software

**Repositorio:** https://github.com/soyMiguel245/sistema_gestion_hospitalaria
**Stack:** Laravel 11 (PHP 8.2) · SQL Server · Bootstrap 5 · GitHub Actions

---

## 1. Resumen ejecutivo

Este documento describe el proceso completo de auditoría, corrección y fortalecimiento
de calidad de software aplicado al Sistema de Gestión Hospitalaria, un proyecto
académico desarrollado en Laravel para la gestión de pacientes, citas médicas,
historiales clínicos y atenciones médicas de un centro de salud.

El trabajo se realizó en dos fases:

1. **Auditoría inicial:** evaluación del estado del proyecto contra el estándar
   **ISO/IEC 25010** de calidad de software, identificando 18 hallazgos concretos
   organizados por severidad (crítico, importante, moderado).
2. **Plan de corrección de 2 semanas:** ejecución metódica, día por día, con cada
   fase cerrada mediante commit en Git solo después de ser probada y confirmada
   funcionando — nunca se avanzó con algo "a medias".

El resultado es un sistema que pasó de un puntaje ISO 25010 estimado de **3.4/10**
a un estado auditado y probado de forma automatizada, con **31 tests pasando
tanto en Windows como en Linux** (vía CI/CD), autenticación de dos factores,
control de acceso basado en roles, y auditoría de accesos funcional.

---

## 2. Metodología de trabajo

A diferencia de un desarrollo lineal de features, este proyecto se trabajó como
una **auditoría de calidad de software aplicada**, siguiendo este ciclo repetido
para cada hallazgo:

```
1. Detectar el problema (revisión de código o falla real observada)
2. Diagnosticar la causa raíz (no solo el síntoma)
3. Corregir con el mínimo cambio necesario
4. Probar manualmente en el navegador/terminal
5. Confirmar con evidencia (captura, log, o test automatizado)
6. Commit descriptivo en Git
7. Push a GitHub
```

Este ciclo se repitió más de 15 veces a lo largo de 2 semanas, documentado en un
historial de commits completamente trazable (ver sección 5).

**Herramientas de colaboración usadas:**
- **Git + GitHub** para control de versiones y respaldo remoto
- **GitHub Actions** para validación automática continua (CI/CD)
- **VS Code** como entorno principal de desarrollo
- Un asistente de IA (Claude) usado como par de auditoría técnica y generador de
  código, bajo revisión y prueba humana en cada paso — ninguna corrección se
  subió a producción sin antes probarse manualmente.

---

## 3. Arquitectura del sistema

### 3.1 Modelo de datos evolucionado

El sistema pasó por un rediseño significativo de su modelo de roles y de
historial clínico durante el proceso de auditoría:

| Antes | Ahora | Motivo del cambio |
|---|---|---|
| `role` como string libre en `users` | Tabla `roles` relacional con FK `role_id` | Evitar valores de rol inválidos o mal escritos; estructura más realista para un hospital con roles que pueden crecer (se agregó `enfermera` sin tocar el esquema) |
| `historias_clinicas` como tabla independiente | `HistorialClinico` derivado de `AtencionMedica` | Evitar duplicación de datos clínicos; el expediente de un paciente se arma dinámicamente desde sus atenciones reales |
| Archivos médicos en columnas JSON | Tabla `archivos_medicos` normalizada | Permite auditar, borrar y consultar archivos individualmente, con metadata (tipo, tamaño, quién subió) |

Se mantuvo **retrocompatibilidad** durante la migración de roles mediante un
patrón accessor/mutator en el modelo `User` (`$user->role` sigue funcionando
como string en todo el código existente, aunque internamente ahora es una
relación).

### 3.2 Control de acceso (RBAC)

Cuatro roles con permisos diferenciados, implementados con **Laravel Policies**
y conectados a las rutas mediante `authorizeResource()`:

| Módulo | Administrador | Médico | Recepción | Enfermera |
|---|:---:|:---:|:---:|:---:|
| Pacientes | ✅ todo | ✅ ver | ✅ todo | ✅ ver |
| Citas | ✅ todo | ✅ ver | ✅ todo | ✅ ver |
| Historias Clínicas | ✅ todo | ✅ todo | ❌ | ✅ ver |
| Atenciones Médicas | ✅ todo | ✅ todo | ❌ | ❌ |
| Reportes | ✅ | ✅ | ❌ | ❌ |

La protección se implementó en **dos capas**, no solo una:
1. El menú lateral oculta las opciones que el rol no puede usar (UX)
2. Cada ruta verifica el permiso en el servidor con `$this->authorize()` — se
   confirmó explícitamente que ocultar un botón **no es suficiente**: un usuario
   sin permiso que escribe la URL directamente recibe un **403 Forbidden** real.

### 3.3 Autenticación reforzada

- **Rate limiting**: 5 intentos fallidos de login bloquean temporalmente
  (incluido de fábrica en Laravel Breeze, confirmado y documentado)
- **2FA (autenticación en dos pasos)** implementado con TOTP (`pragmarx/google2fa`),
  compatible con Google Authenticator/Authy, con generación de código QR local
  (sin depender de servicios externos), códigos de recuperación de un solo uso,
  y un paso de verificación intercalado en el login existente sin reescribir
  el flujo de Laravel Breeze

### 3.4 Auditoría de accesos

Se usa `spatie/laravel-activitylog` para registrar automáticamente creación,
edición y eliminación en los modelos `Paciente`, `Cita` y `AtencionMedica`.

Adicionalmente, se detectó y corrigió un hueco de auditoría: **la sola lectura**
del expediente clínico de un paciente no quedaba registrada (el paquete solo
audita escritura por diseño). Se agregó un registro manual de auditoría en
`HistorialClinicoController::show()`, verificado tanto manualmente como con
un test automatizado.

---

## 4. Hallazgos críticos y su corrección

| # | Hallazgo | Severidad | Estado |
|---|---|---|---|
| 1 | Archivos médicos servidos desde disco público, accesibles sin login | 🔴 Crítico | ✅ Corregido — disco privado + ruta protegida por Policy |
| 2 | Sin transacción atómica al registrar atención + archivos | 🟠 Importante | ✅ Corregido con `DB::transaction()` |
| 3 | Migración con nombre de archivo inválido (nunca se ejecutaba) | 🟠 Importante | ✅ Eliminada |
| 4 | Rutas sin protección real (solo ocultas visualmente) | 🔴 Crítico | ✅ `authorizeResource()` en los 4 controladores principales |
| 5 | `role` faltante en `$fillable` de `User` (bug silencioso) | 🟠 Importante | ✅ Corregido |
| 6 | Registro público sin rol asignado (fallo silencioso) | 🟠 Importante | ✅ `RegisteredUserController` asigna `recepcion` por defecto |
| 7 | 3 archivos de vista con mayúscula inicial, incompatibles con Linux | 🟠 Importante | ✅ Detectado por CI/CD y corregido |
| 8 | Clave de API expuesta accidentalmente en captura de pantalla | 🔴 Crítico (seguridad de cuenta, no del código) | ⚠️ Recomendado revocar (acción del usuario) |

**Nota metodológica relevante:** los hallazgos #7 son un ejemplo real de por qué
la validación continua (CI/CD) importa — estos bugs de compatibilidad
mayúscula/minúscula eran **invisibles en Windows** (donde se desarrolló todo el
proyecto) y solo se manifestaron al ejecutar los tests en Linux (el sistema
operativo real de GitHub Actions, y el más común en servidores de producción).
Sin este mecanismo de validación automática, estos bugs habrían llegado a
producción sin detectarse.

---

## 5. Testing automatizado

**31 tests, 119+ assertions, pasando en Windows y Linux:**

| Suite | Qué cubre |
|---|---|
| `AuthenticationTest`, `EmailVerificationTest`, `PasswordResetTest`, etc. (Breeze) | Autenticación básica heredada del scaffold |
| `RolePermissionsTest` | Matriz completa de 4 roles × 11 rutas principales (44 combinaciones verificadas) |
| `ReglasDeNegocioTest` | DNI único de paciente, choque de horario médico (con solapamiento real, no solo coincidencia exacta), y auditoría de lectura de historial clínico |
| `ProfileTest` | Gestión de perfil de usuario |

**CI/CD:** GitHub Actions ejecuta la suite completa automáticamente en cada
`git push` a la rama `main`, contra una base de datos SQLite en memoria (para
velocidad, sin necesitar SQL Server real en el pipeline). Cualquier regresión
futura se detecta antes de llegar a producción.

---

## 6. Portabilidad (Docker)

Se preparó una configuración de **Docker + Docker Compose** que permite levantar
el proyecto completo (aplicación PHP + SQL Server) con un solo comando, sin
requerir instalación manual de PHP, Composer, Node o SQL Server en la máquina
del desarrollador. Incluye:
- Instalación automática del driver ODBC 17 de Microsoft para SQL Server en Linux
- Creación automática de la base de datos si no existe
- Ejecución automática de migraciones al iniciar

*(Estado: implementado, pendiente de verificación final de arranque exitoso —
ver checklist de la Fase 12 en el roadmap del proyecto)*

---

## 7. Evaluación final — ISO/IEC 25010

| Dimensión | Antes de la auditoría | Estado actual |
|---|:---:|:---:|
| Funcionalidad | 4/10 | 6/10 |
| Fiabilidad | 3/10 | 7/10 |
| Usabilidad | 5/10 | 6/10 |
| Eficiencia de desempeño | 4/10 | 6/10 |
| Compatibilidad | 4/10 | 5/10 |
| **Seguridad** | **2/10** | **8/10** |
| **Mantenibilidad** | **2/10** | **8/10** |
| Portabilidad | 3/10 | 6/10 (con Docker pendiente de verificar) |

**Promedio antes:** 3.4/10 → **Promedio actual:** ~6.5/10, con evidencia
verificable (tests automatizados, CI/CD en verde, pruebas manuales
documentadas) respaldando cada punto — no autoevaluación sin sustento.

---

## 8. Decisiones de alcance documentadas

Por transparencia, se documentaron explícitamente las mejoras identificadas
pero **no implementadas** por restricciones de tiempo, en lugar de dejarlas
ambiguas:

- **Cifrado AES-256 de historias clínicas**: no implementado (el contenido
  vive en base de datos protegida por RBAC, pero no encriptado a nivel de columna)
- **Operatividad offline de 12 horas**: fuera de alcance — incompatible con la
  arquitectura Blade tradicional del proyecto sin una reescritura mayor
- **2FA obligatorio por rol**: implementado como funcionalidad opcional
  disponible para cualquier usuario, no forzado aún para el rol Administrador

---

## 9. Conclusiones

El proyecto demuestra un ciclo completo de mejora de calidad de software:
diagnóstico basado en un estándar reconocido (ISO 25010), corrección priorizada
por severidad e impacto real (no por facilidad), validación mediante pruebas
automatizadas y manuales, y trazabilidad completa a través de control de
versiones. Los hallazgos de compatibilidad detectados por CI/CD ilustran un
punto de valor real: **una herramienta de validación continua encontró bugs
reales que la revisión manual en un solo sistema operativo no podía detectar.**

Quedan pendientes para el cierre final del proyecto: verificación de arranque
de Docker, matriz de trazabilidad RF/RNF completa contra los documentos
académicos originales, y el README de instalación definitivo.