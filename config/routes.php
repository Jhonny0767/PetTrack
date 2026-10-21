<?php
/**
 * config/routes.php
 * Mapa de rutas de la aplicación.
 * Formato: $router->get('/url', 'Controlador@metodo');
 */

/** @var \App\Core\Router $router */

// ============================================
// AUTENTICACIÓN (público)
// ============================================
$router->get('/',              'AuthController@showLogin');
$router->post('/login',        'AuthController@login');
$router->post('/registro',     'AuthController@register');
$router->post('/cerrar-sesion','AuthController@logout');

// ============================================
// ZONA DE USUARIO (requiere login)
// ============================================

// Inicio / Dashboard
$router->get('/dashboard', 'HomeController@index');
$router->get('/blog',      'HomeController@blog');
$router->get('/contacto',  'HomeController@contact');

// Perfil del usuario
$router->get('/perfil',           'PerfilController@show');
$router->post('/perfil',          'PerfilController@update');

// Mascotas
$router->post('/mascotas/crear',              'MascotaController@create');
$router->get('/mascotas/editar/{id}',         'MascotaController@edit');
$router->post('/mascotas/actualizar',         'MascotaController@update');
$router->post('/mascotas/eliminar',           'MascotaController@delete');
$router->post('/mascotas/vacunas/eliminar',   'MascotaController@deleteVacuna');
$router->post('/mascotas/visitas/eliminar',   'MascotaController@deleteVisita');

// PQRS (usuario)
$router->get('/pqrs',        'PqrsController@showForm');
$router->post('/pqrs',       'PqrsController@submit');

// Notificaciones (para cualquier usuario logueado)
$router->get('/notificaciones',                 'NotificacionController@index');
$router->post('/notificaciones/marcar-leida',   'NotificacionController@marcarLeida');
$router->post('/notificaciones/marcar-todas',   'NotificacionController@marcarTodas');

// ============================================
// ZONA DE ADMIN (requiere rol admin)
// ============================================

// CRUD principal
$router->get('/admin',                          'AdminController@index');
$router->get('/admin/editar/{id}',              'AdminController@edit');
$router->post('/admin/actualizar',              'AdminController@update');
$router->post('/admin/eliminar',                'AdminController@delete');
$router->post('/admin/cambiar-rol',             'AdminController@cambiarRol');
$router->post('/admin/eliminar-mascota',        'AdminController@deleteMascota');
$router->post('/admin/eliminar-vacuna',         'AdminController@deleteVacuna');
$router->post('/admin/eliminar-visita',         'AdminController@deleteVisita');

// Generación de PDF
$router->get('/admin/pdf',                      'AdminController@pdf');

// Gestión de PQRS
$router->get('/admin/pqrs',                     'AdminController@pqrsList');
$router->get('/admin/pqrs/{id}',                'AdminController@pqrsView');
$router->post('/admin/pqrs/responder',          'AdminController@pqrsRespond');