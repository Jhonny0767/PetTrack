# CONTEXTO — PetTrack

**Última sesión:** 12/10/2026

## Estado actual

- **Fases completadas:** 7.1 (Testing), 7.2 (JS ES6), 7.3 (Cacheo), 8.1 (Roles)
- **Fase en curso:** 8.2 (Notificaciones) — sin empezar
- **Tests:** 33 pasando (`.\vendor\bin\phpunit --testdox`)

## Cómo retomar

Decirle al asistente:

> "Retomemos. Vamos con 8.2 (Notificaciones al usuario)."

## Infraestructura añadida

- `app/Core/Roles.php` — Jerarquía admin > moderador > usuario
- `Controller::requireRole()`, `requireModerator()`, `hasRole()`
- `Controller::render(..., $cacheTtl)` — Cache opcional (ej: `, 'main', 300`)
- `Usuario::actualizarRol($id, $rol)`
- `AdminController::cambiarRol()` + ruta `/admin/cambiar-rol`
- Selector de rol en `/admin`
- Moderador puede ver PQRS, bloqueado del resto de admin
- `tests/bootstrap.php` + `phpunit.xml`
- Composer + PHPUnit 11.5.57

## Comandos útiles

- Tests: `.\vendor\bin\phpunit --testdox`
- Limpiar cache vistas: `Remove-Item storage\cache\views\*.html`
- MySQL: `& "C:\xampp\mysql\bin\mysql.exe" -u root login_register_db -e "..."`

## Pendientes menores

- Botón "Volver al panel" en PQRS apunta a `/admin` (falla 403 si eres moderador). Arreglar en 8.2.
- Carpeta `crud/` en raíz — verificar si aún se usa.
- Activar extensión `zip` en PHP para acelerar Composer.

# CONTEXTO — PetTrack

## Estado actual
- **Fases completadas:** 7.1, 7.2, 7.3, 8.1
- **Fase en curso:** 8.2 (Notificaciones) — esperando crear tabla
- **Tests:** 33 pasando (`.\vendor\bin\phpunit --testdox`)

## Lo que hicimos hoy
- Reparado el flujo de login: el moderador ahora entra directo a `/admin/pqrs` (no al dashboard normal).
- Arreglado el botón "Volver" en PQRS: los moderadores ven "Volver a mi perfil" (sin error 403).
- Confirmado que no se puede borrar a un admin (es intencional por seguridad).

## Pendientes menores (para el final)
- Ocultar botones "Editar" y "Eliminar" en la tabla de admin cuando el usuario sea `admin` (para evitar el pantallazo blanco de "No se puede eliminar").
- Permitir que un admin degrade a otro admin a usuario/moderador (quitar la restricción en `AdminController::cambiarRol`).
- Botón "Volver al panel" en PQRS apuntando a `/admin` (ya resuelto).
- Verificar si la carpeta `crud/` aún se usa.
- Activar extensión `zip` en PHP.

## Siguiente paso (mañana)
1. Crear la tabla `notificaciones` (ejecutar el archivo `schema_notificaciones.sql`).
2. Crear el modelo `Notificacion.php`.
3. Crear el controlador y la campanita en el layout.

# CONTEXTO — PetTrack

## Estado actual
- **Fases completadas:** 7.1, 7.2, 7.3, 8.1, 8.2
- **Fase en curso:** Ajustes finales / 9.x (Production-ready)
- **Tests:** 33 pasando (`.\vendor\bin\phpunit --testdox`)

## Cómo retomar
Decirle al asistente:
> "Retomemos. Vamos con los pendientes de la Fase 8.2 antes de arrancar 9.x."

## Infraestructura añadida (Fase 8.2)
- Tabla `notificaciones` en BD.
- Modelo `Notificacion.php` (crear, leer, contar, marcar leídas).
- Controlador `NotificacionController.php` y vistas para la bandeja.
- Campanita con contador inyectada automáticamente en `layouts/main.php` y `layouts/admin.php`.
- Notificaciones automáticas al: cambiar rol, crear PQRS y responder PQRS.

## Comandos útiles
- Tests: `.\vendor\bin\phpunit --testdox`
- Limpiar cache vistas: `Remove-Item storage\cache\views\*.html`
- MySQL: `& "C:\xampp\mysql\bin\mysql.exe" -u root login_register_db -e "..."`

## Pendientes menores (Deuda técnica para resolver AHORA)
- [ ] Ocultar botones "Editar" y "Eliminar" en `admin/index.php` para usuarios con rol `admin` (evitar error 500).
- [ ] Permitir que un admin degrade a otro admin a moderador/usuario en `AdminController::cambiarRol` (quitar la restricción de "No se puede modificar a otro admin").
- [ ] Activar extensión `zip` en PHP para acelerar Composer.

# CONTEXTO — PetTrack

## Estado actual
- **Fases completadas:** 7.1, 7.2, 7.3, 8.1, 8.2
- **Fase en curso:** 9.x (Production-ready)
- **Tests:** 33 pasando (`.\vendor\bin\phpunit --testdox`)

## Cómo retomar
Decirle al asistente:
> "Retomemos. Vamos a empezar con la Fase 9 (Production-ready)."

## Logros recientes
- **Fase 8.2 (Notificaciones):** Sistema completo. Tabla en BD, modelo, controlador, campanita en layouts (main y admin), y avisos automáticos por PQRS y cambios de rol.
- **Deuda 8.1 resuelta:** Los admins ahora están protegidos (no se pueden borrar sin el candado), pero un admin puede degradar a otro con una ventana de advertencia previa.

## Comandos útiles
- Tests: `.\vendor\bin\phpunit --testdox`
- Limpiar cache vistas: `Remove-Item storage\cache\views\*.html`
- MySQL: `& "C:\xampp\mysql\bin\mysql.exe" -u root login_register_db -e "..."`

## Pendientes menores
- Verificar si la carpeta `crud/` en la raíz se puede eliminar.
- Activar extensión `zip` en PHP.

# CONTEXTO — PetTrack

## Estado actual
- **Fases completadas:** 7.1, 7.2, 7.3, 8.1, 8.2, 9.x
- **Estado del proyecto:** Production-ready (Listo para desplegar)
- **Tests:** 33 pasando (`.\vendor\bin\phpunit --testdox`)

## Resumen de logros
- **Testing:** Configuración de PHPUnit con 33 pruebas (Unit e Integration).
- **Frontend:** Migración de JS a módulos ES6 y optimización de carga.
- **Rendimiento:** Sistema de cacheo de vistas en `storage/cache/views`.
- **Seguridad:** Sistema de roles (Admin > Moderador > Usuario), protección CSRF, blindaje de carpetas con `.htaccess` (bloqueo de `.env`, `app`, `config`), headers de seguridad (CSP, HSTS) y protección anti-ejecución en `storage/uploads`.
- **Notificaciones:** Sistema completo (BD, modelo, controlador, campanita en layouts) con avisos automáticos por cambios de rol y respuestas a PQRS.
- **Manejo de errores:** Páginas 403, 404 y 500 personalizadas y capturador global de excepciones en `bootstrap.php`.

## Comandos útiles
- Correr Tests: `.\vendor\bin\phpunit --testdox`
- Limpiar cache de vistas: `Remove-Item storage\cache\views\*.html`
- Consola MySQL: `& "C:\xampp\mysql\bin\mysql.exe" -u root login_register_db -e "..."`
- Entorno: `APP_ENV=local` en `.env` para desarrollo (cambiar a `production` al desplegar).

## Próximos pasos (si se desea continuar)
- Despliegue en un servidor real (Hostinger, DigitalOcean, etc.).
- Configuración de correos electrónicos para recuperación de contraseñas.
- Pasarela de pagos (si el negocio lo requiere).