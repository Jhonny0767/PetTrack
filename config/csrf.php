<?php
/**
 * csrf.php
 * Helper para generación y validación de tokens CSRF.
 * También gestiona el timeout de sesión por inactividad.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// Timeout de sesión por inactividad
// ============================================
// Cargar el .env si no está cargado aún
if (!function_exists('env')) {
    $loader = __DIR__ . '/../../config/env_loader.php';
    if (file_exists($loader)) {
        require_once $loader;
    }
}

$session_timeout = function_exists('env') ? (int) env('SESSION_TIMEOUT', 1800) : 1800;

if (isset($_SESSION['usuario_id'])) {
    $now = time();
    $last = $_SESSION['last_activity'] ?? $now;

    // Si pasó el timeout desde la última actividad
    if (($now - $last) > $session_timeout) {
        // Destruir sesión
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();

        http_response_code(401);
                $base = function_exists('env') ? env('APP_BASE_URL', '/LOGIN-REGISTER') : '/LOGIN-REGISTER';
        echo '<script>alert("Sesión expirada por inactividad. Inicia sesión de nuevo."); window.location = "' . $base . '/index.php";</script>';
        exit();
    }

    // Actualizar marca de tiempo de la última actividad
    $_SESSION['last_activity'] = $now;
}

// ============================================
// Generación y validación de tokens CSRF
// ============================================

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_validate(?string $token): void {
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], (string)$token)) {
        http_response_code(403);
        exit("Token CSRF inválido.");
    }
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}