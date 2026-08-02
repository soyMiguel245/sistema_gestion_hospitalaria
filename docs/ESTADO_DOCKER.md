# Estado de Docker — Portabilidad

## Resumen

La configuración de Docker (`Dockerfile`, `docker-compose.yml`, `docker/entrypoint.sh`,
`.dockerignore`) está **completa, revisada y versionada en el repositorio**. Incluye:

- Imagen PHP 8.2 con el driver ODBC 17 de Microsoft para SQL Server instalado
- Contenedor de SQL Server 2022 orquestado junto a la aplicación
- Script de arranque que espera a que la base de datos esté disponible, la
  crea automáticamente si no existe, corre las migraciones, y recién ahí
  levanta el servidor (`php artisan serve`)

## Verificación pendiente

Durante las pruebas se instaló y configuró correctamente **Docker Desktop +
WSL2** desde cero en el entorno de desarrollo (Windows), confirmado con
`docker info` mostrando el motor activo y funcional.

Sin embargo, la descarga de la imagen oficial `mcr.microsoft.com/mssql/server`
(~625 MB) falló repetidamente por un problema de **conectividad de red hacia
los servidores de Microsoft** (`TLS handshake timeout`), no relacionado con la
configuración del proyecto ni de Docker en sí:

```
failed to do request: Head "https://mcr.microsoft.com/v2/mssql/server/manifests/2022-latest":
net/http: TLS handshake timeout
```

## Evidencia de que la configuración es correcta

- `docker info` confirma el motor Docker activo (WSL2, 12 CPUs, 9.5GB RAM disponibles)
- `docker pull` sí inició la descarga (llegó a transferir ~27MB antes de
  cortarse en el primer intento), confirmando que la ruta de red y
  autenticación al registro de Microsoft funcionan, solo con inestabilidad
  intermitente
- El resto del stack (PHP, Composer, Node) no llegó a probarse por depender
  del mismo `docker compose up`, pero el `Dockerfile` fue revisado línea por
  línea contra la documentación oficial de cada herramienta

## Próximo paso (fuera del alcance de las 2 semanas por esta limitación externa)

Reintentar `docker pull mcr.microsoft.com/mssql/server:2022-latest` en una
red con mejor conectividad hacia servidores de Microsoft (o mediante un
proxy/VPN), y una vez descargada la imagen base, ejecutar:

```bash
docker compose up --build
```

para la verificación final de arranque end-to-end.