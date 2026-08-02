# Estado de Docker — Portabilidad

## Resumen

**Verificado end-to-end el 02/08/2026.** `docker compose up -d --build` levanta
la aplicación completa desde cero: construye la imagen, crea el contenedor de
SQL Server, espera a que la base de datos esté disponible, la crea
automáticamente si no existe, corre las 18 migraciones, y levanta el servidor
Laravel — todo sin intervención manual.

Confirmado con una petición HTTP real: