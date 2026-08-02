# Decisiones de Alcance — Día 10

## 2FA (Autenticación de dos factores) — Fuera de alcance

**Decisión:** No implementado en esta entrega.

**Justificación técnica:** Implementar 2FA con `laravel/fortify` requiere:
- Reestructurar el flujo de login actual (Laravel Breeze) para coexistir con Fortify
- Generación y validación de códigos QR (Google Authenticator / apps TOTP)
- Manejo de códigos de recuperación de emergencia
- Vistas nuevas para activar/desactivar 2FA por usuario
- Testing adicional de todo el flujo

Esto representa el mayor esfuerzo individual de todo el plan de 2 semanas,
y con el tiempo restante se priorizó CI/CD, Docker y documentación final,
que dan cobertura más amplia de calidad de software en el tiempo disponible.

**Mitigación actual sin 2FA:**
- Rate limiting ya activo (5 intentos fallidos → bloqueo temporal), que
  cubre el vector de ataque más común (fuerza bruta) aunque no reemplaza
  la protección de 2FA ante credenciales robadas/filtradas
- RBAC granular limita el daño incluso si una cuenta se ve comprometida
  (ej. una cuenta de Recepción comprometida no puede ver historias clínicas)

**Recomendación para una futura iteración:** priorizar 2FA obligatorio al
menos para el rol Administrador antes de cualquier despliegue a producción
real con pacientes reales.
