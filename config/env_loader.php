<?php
/**
 * env_loader.php
 * Carga las variables de entorno desde el archivo .env
 * y las expone con getenv() y $_ENV.
 */

function load_env(string $path): void {
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }

        if (strpos($line, '=') === false) {
            continue;
        }

        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        if (preg_match('/^"(.*)"$/', $value, $m) || preg_match("/^'(.*)'$/", $value, $m)) {
            $value = $m[1];
        }

        if (getenv($key) === false) {
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

/**
 * Devuelve el valor de una variable de entorno o el valor por defecto.
 */
function env(string $key, $default = null) {
    $value = getenv($key);
    return $value === false ? $default : $value;
}

// Cargar automáticamente el .env de la raíz del proyecto
load_env(__DIR__ . '/.env');

/**
 * Devuelve la URL base de la aplicación, con la barra final opcional.
 * Útil para generar rutas absolutas que funcionen en local y en producción.
 *
 * Uso:
 *   echo base_url();              // "/LOGIN-REGISTER"
 *   echo base_url('/index.php');  // "/LOGIN-REGISTER/index.php"
 */
function base_url(string $path = ''): string {
    $base = rtrim(env('APP_BASE_URL', ''), '/');
    if ($path === '') {
        return $base;
    }
    return $base . '/' . ltrim($path, '/');
}