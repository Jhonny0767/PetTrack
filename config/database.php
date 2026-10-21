<?php
/**
 * config/database.php
 * Conexión única a la base de datos (mysqli).
 * Todos los archivos de la app deberían usar este archivo.
 */

require_once __DIR__ . '/env_loader.php';

$host = env('DB_HOST', 'localhost');
$user = env('DB_USER', 'root');
$pass = env('DB_PASS', '');
$name = env('DB_NAME', 'login_register_db');

$conexion = mysqli_connect($host, $user, $pass, $name);

if (!$conexion) {
    http_response_code(500);
    die("Error de conexión a la base de datos: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8mb4");