<?php
/**
 * public/index.php
 * FRONT CONTROLLER: único punto de entrada del sistema MVC.
 * Todas las URLs nuevas pasan por aquí.
 */

// ============================================
// 1. Arrancar la aplicación
// ============================================
require_once __DIR__ . '/../bootstrap.php';

use App\Core\Router;

// ============================================
// 2. Crear el router y cargar las rutas
// ============================================
$router = new Router();

require_once __DIR__ . '/../config/routes.php';

// ============================================
// 3. Despachar la petición
// ============================================
$uri = $_SERVER['REQUEST_URI'] ?? '/';

// Quitar el prefijo de la carpeta del proyecto
// (porque estamos en /LOGIN-REGISTER/public/)
$basePath = '/LOGIN-REGISTER';
if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}

// Si la URL es /public/... también lo quitamos
if (strpos($uri, '/public') === 0) {
    $uri = substr($uri, 7);
}

// Asegurar que empieza con /
if ($uri === '' || $uri[0] !== '/') {
    $uri = '/' . $uri;
}

$metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// ============================================
// 4. Ejecutar el router
// ============================================
try {
    $router->dispatch($uri, $metodo);
} catch (Throwable $e) {
    if (env('APP_ENV', 'local') === 'local') {
        echo "<h1>Error del Router</h1>";
        echo "<pre>" . $e->getMessage() . "</pre>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    } else {
        http_response_code(500);
        require __DIR__ . '/../500.php';
    }
}