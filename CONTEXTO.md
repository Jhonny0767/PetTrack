# CONTEXTO — PetTrack

**Última sesión:** 24/10/2026

## ⚠️ Actualización Crítica de Seguridad (Octubre 2026)
- **Migración de Entorno:** Debido a las vulnerabilidades críticas de PHP (PHP2Shell), se reinstaló el entorno local.
- **Nueva versión:** XAMPP actualizado a **PHP 8.3.36** (versión parcheada).
- **Base de Datos:** MariaDB 10.4.32 restaurada exitosamente desde el archivo `.sql`.
- **Estado:** Proyecto verificado al 100% (login, roles, PQRS, notificaciones y dashboard funcionando).

## Estado del Proyecto
- **Fases completadas:** 7.1, 7.2, 7.3, 8.1, 8.2, 9.x
- **Estado actual:** Production-ready (Listo para desplegar)
- **Tests:** 33 pasando (`.\vendor\bin\phpunit --testdox`)

## Resumen de Logros Principales
- **Testing:** Configuración de PHPUnit con 33 pruebas (Unit e Integration).
- **Seguridad:** Roles (Admin > Moderador > Usuario), protección CSRF, blindaje de `.htaccess`, headers HTTP y bloqueo de ejecución en `storage/uploads`.
- **Notificaciones:** Sistema completo (BD, modelo, controlador, campanita) con avisos automáticos.
- **Producción:** Páginas de error personalizadas (403, 404, 500) y capturador global de excepciones.

## Comandos Útiles
- Correr Tests: `.\vendor\bin\phpunit --testdox`
- Limpiar cache de vistas: `Remove-Item storage\cache\views\*.html`
- Consola MySQL: `& "C:\xampp\mysql\bin\mysql.exe" -u root login_register_db -e "..."`
- Entorno: `APP_ENV=local` en `.env` para desarrollo.

## Próximos Pasos (Futuro)
- Despliegue en servidor real (Hostinger, DigitalOcean, etc.).
- Configuración de correos para recuperación de contraseñas.
- Arreglar botón "Volver al panel" en PQRS para moderadores (si aún queda pendiente).