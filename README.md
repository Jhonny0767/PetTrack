# 🐾 PetTrack

Aplicación web completa para la gestión de usuarios y el historial médico de sus mascotas. Desarrollada con arquitectura MVC, enfocada en la seguridad y la experiencia de usuario.

## 🚀 Tecnologías Utilizadas

- **Backend:** PHP (Patrón MVC)
- **Base de Datos:** MySQL (PDO)
- **Frontend:** HTML5, CSS3, JavaScript (ES6)
- **Herramientas:** Composer, Git, PHPUnit

## ✨ Características Principales

- **Autenticación Segura:** Registro y login con encriptación de contraseñas (Bcrypt) y protección CSRF.
- **Sistema de Roles Granular:** Jerarquía de permisos (Admin > Moderador > Usuario).
- **Gestión de Mascotas:** CRUD completo para el registro de mascotas, vacunas y visitas veterinarias.
- **Módulo PQRS:** Peticiones, Quejas, Reclamos y Sugerencias con sistema de respuestas.
- **Notificaciones Internas:** Campana de avisos automáticos por cambios de rol o respuestas a PQRS.
- **Seguridad Blindada:** Protección de rutas, bloqueo de acceso a archivos sensibles (.env, app/) y headers de seguridad (CSP, HSTS).
- **Manejo de Errores:** Páginas personalizadas para errores 403, 404 y 500.

## ⚙️ Instalación y Configuración Local

1. Clona este repositorio en tu servidor local (XAMPP/WAMP):
   ```bash
   git clone https://github.com/Jhonny0767/PetTrack.git
