<?php
/**
 * bootstrap.php
 * Arranca la aplicación:
 *  1. Carga el autoloader de clases
 *  2. Carga las variables de entorno (.env)
 *  3. Inicia la sesión
 *  4. Carga las rutas
 */

// ============================================
// 1. Autoloader simple para las clases de App\*
// ============================================
spl_autoload_register(function (string $clase) {
    // Solo nos interesan las clases del namespace App\
    $prefijo = 'App\\';
    if (strpos($clase, $prefijo) !== 0) {
        return;
    }

    // Convertir App\Core\Router → Core/Router
    $rutaRelativa = substr($clase, strlen($prefijo));
    $rutaRelativa = str_replace('\\', '/', $rutaRelativa);

    $archivo = __DIR__ . '/app/' . $rutaRelativa . '.php';

    if (file_exists($archivo)) {
        require_once $archivo;
    }
});

// ============================================
// 2. Cargar variables de entorno (.env)
// ============================================
require_once __DIR__ . '/config/env_loader.php';

// ============================================
// 3. Cargar helpers (csrf + base_url)
// ============================================
require_once __DIR__ . '/config/csrf.php';

// ============================================
// 4. Iniciar sesión (con configuración segura)
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// ============================================
// 5. Configuración de errores (según entorno)
// ============================================
if (env('APP_ENV', 'local') === 'local') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
}

// ============================================
// 6. Manejadores globales de errores (Production-ready)
// ============================================
// Esto asegura que si algo falla, el usuario vea la página 500.php
// en lugar de un pantallazo blanco con errores del sistema.

// 1. Capturar excepciones no controladas
set_exception_handler(function ($exception) {
    error_log('Excepción no capturada: ' . $exception->getMessage() . ' en ' . $exception->getFile() . ':' . $exception->getLine());
    
    if (env('APP_ENV', 'local') !== 'local') {
        // En producción mostramos la página 500 bonita
        http_response_code(500);
        require __DIR__ . '/500.php';
        exit;
    } else {
        // En local (desarrollo), mostramos el error real para poder depurar
        echo "<h1>Error en el sistema</h1>";
        echo "<p>" . $exception->getMessage() . "</p>";
        echo "<pre>" . $exception->getTraceAsString() . "</pre>";
        exit;
    }
});

// 2. Capturar errores fatales (Fatal Error, Parse Error, etc.)
register_shutdown_function(function () {
    $error = error_get_last();
    
    // Verificamos si hubo un error fatal real
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        error_log('Error fatal: ' . $error['message'] . ' en ' . $error['file'] . ':' . $error['line']);
        
        if (env('APP_ENV', 'local') !== 'local') {
            // Para evitar duplicar la pantalla si la excepción ya se mostró
            if (!headers_sent()) {
                http_response_code(500);
            }
            require __DIR__ . '/500.php';
            exit;
        }
    }
});