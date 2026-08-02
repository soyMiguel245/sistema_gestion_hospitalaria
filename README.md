# Sistema de Gestion Hospitalaria

Sistema web para la gestion de pacientes, citas medicas, historiales clinicos
y atenciones medicas, desarrollado en Laravel 11 como proyecto academico de
la Universidad Nacional de Huancavelica (Escuela de Ingenieria de Sistemas).

**Estado del proyecto:** en desarrollo activo, sometido a un proceso de
auditoria y mejora de calidad de software. Ver `docs/INFORME_TECNICO_AVANZADO.md`
para el detalle completo del proceso, y `docs/MATRIZ_TRAZABILIDAD.md` para el
estado real de cumplimiento de requerimientos.

---

## Stack tecnologico

- **Backend:** Laravel 11 (PHP 8.2)
- **Base de datos:** SQL Server (driver sqlsrv)
- **Frontend:** Blade + Bootstrap 5 + Bootstrap Icons
- **Autenticacion:** Laravel Breeze + 2FA (TOTP con pragmarx/google2fa)
- **Autorizacion:** Laravel Policies (RBAC con 4 roles)
- **Auditoria:** spatie/laravel-activitylog
- **Testing:** PHPUnit, 31+ tests automatizados
- **CI/CD:** GitHub Actions (corre la suite completa en cada push)
- **Contenedores:** Docker + Docker Compose (ver seccion de instalacion)

## Roles del sistema

| Rol | Permisos principales |
|---|---|
| Administrador | Acceso total a todos los modulos |
| Medico | Pacientes, citas, historias clinicas, atenciones |
| Recepcion | Pacientes, citas (sin acceso a datos clinicos) |
| Enfermera | Pacientes, citas, historias clinicas (solo lectura) |

---

## Instalacion local (sin Docker)

### Requisitos
- PHP 8.2+
- Composer
- Node.js 20+
- SQL Server (local o remoto), con el driver ODBC 17 + extension sqlsrv/pdo_sqlsrv para PHP

### Pasos

git clone https://github.com/soyMiguel245/sistema_gestion_hospitalaria.git
cd sistema_gestion_hospitalaria

composer install
npm install
npm run build

cp .env.example .env
php artisan key:generate

Edita el archivo .env con los datos de tu conexion a SQL Server:

DB_CONNECTION=sqlsrv
DB_HOST=localhost
DB_PORT=1433
DB_DATABASE=sistema_gestion_hospitalaria
DB_USERNAME=sa
DB_PASSWORD=tu_password

Luego:

php artisan migrate
php artisan db:seed
php artisan serve

La aplicacion queda disponible en http://localhost:8000

---

## Instalacion con Docker (recomendada para evaluacion rapida)

Requiere Docker Desktop instalado y corriendo (en Windows, con WSL2 habilitado).

docker compose up --build

Esto levanta automaticamente:
- El contenedor de la aplicacion (PHP + Laravel)
- Un contenedor de SQL Server 2022
- Crea la base de datos y corre las migraciones al iniciar

La aplicacion queda disponible en http://localhost:8000

Nota: si tienes SQL Server instalado localmente en tu maquina Windows,
detente ese servicio antes de levantar Docker, ya que ambos usan el puerto
1433 por defecto y entraran en conflicto.

---

## Crear un usuario administrador

Por seguridad, el registro publico (/register) asigna el rol recepcion
por defecto. Para crear el primer administrador:

php artisan tinker

$u = App\Models\User::first();
$u->role = 'administrador';
$u->save();

---

## Correr los tests

php artisan test

31+ tests cubren: autenticacion, control de acceso por rol (44 combinaciones
verificadas), reglas de negocio (DNI unico, choque de horarios medicos),
transacciones atomicas y auditoria de accesos.

---

## Documentacion adicional

Toda la documentacion extendida del proyecto esta en la carpeta docs/:

- INFORME_TECNICO_AVANZADO.md - proceso completo de auditoria y mejora de calidad
- MATRIZ_TRAZABILIDAD.md - cumplimiento real de requerimientos funcionales y no funcionales
- DECISION_2FA.md - decisiones de alcance documentadas
- ESTADO_DOCKER.md - estado de verificacion de la configuracion Docker

## Licencia

Proyecto academico desarrollado para la Universidad Nacional de Huancavelica.
Ver archivo LICENSE. Uso educativo.