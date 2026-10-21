<?php
/**
 * Bootstrap para PHPUnit.
 * Carga el autoload de Composer y los helpers del proyecto
 * para poder testear funciones como env(), base_url(), csrf_token().
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Cargar helpers del proyecto (sin arrancar el Router ni sesiones de app)
$configDir = __DIR__ . '/../config';

if (file_exists($configDir . '/env_loader.php')) {
    require_once $configDir . '/env_loader.php';
}
if (file_exists($configDir . '/csrf.php')) {
    require_once $configDir . '/csrf.php';
}