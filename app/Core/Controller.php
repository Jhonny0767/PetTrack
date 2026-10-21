<?php
namespace App\Core;

/**
 * Clase Controller
 * Clase base para todos los controladores.
 * Proporciona métodos útiles: renderizar vistas, redirigir, devolver JSON.
 *
 * Sistema de cache de vistas:
 *   - Opt-in: pasa $cacheTtl > 0 al método render().
 *   - Clave segura: incluye usuario_id + mtime de la vista + datos.
 *   - Ubicación: storage/cache/views/*.html
 */
abstract class Controller
{
    /**
     * Renderiza una vista dentro del layout principal.
     *
     * @param string $vista     Ruta de la vista (ej: 'auth/login')
     * @param array  $datos     Variables que estarán disponibles en la vista
     * @param string $layout    Layout a usar (ej: 'main', 'admin', 'auth')
     * @param int    $cacheTtl  Segundos a cachear. 0 = sin cache (default).
     */
    protected function render(string $vista, array $datos = [], string $layout = 'main', int $cacheTtl = 0): void
    {
        $cacheFile = null;

        // 1) Intentar servir desde cache
        if ($cacheTtl > 0) {
            $cacheFile = $this->getViewCacheFile($vista, $layout, $datos);
            if ($cacheFile !== null
                && file_exists($cacheFile)
                && (time() - filemtime($cacheFile)) < $cacheTtl
            ) {
                readfile($cacheFile);
                return;
            }
        }

        // 2) Renderizado normal
        // Inyectar contador de notificaciones automáticamente en todas las vistas
        if (isset($_SESSION['usuario_id'])) {
            $datos['notificaciones_no_leidas'] = \App\Models\Notificacion::contarNoLeidas((int)$_SESSION['usuario_id']);
        }

        extract($datos);

        $vistaPath  = __DIR__ . '/../Views/' . $vista . '.php';
        $layoutPath = __DIR__ . '/../Views/layouts/' . $layout . '.php';

        if (!file_exists($vistaPath)) {
            http_response_code(500);
            exit("Vista no encontrada: {$vista}");
        }

        // Si no existe el layout, solo renderiza la vista
        if (!file_exists($layoutPath)) {
            if ($cacheTtl > 0 && $cacheFile !== null) {
                ob_start();
                require $vistaPath;
                $html = ob_get_clean();
                $this->guardarEnCache($cacheFile, $html);
                echo $html;
                return;
            }
            require $vistaPath;
            return;
        }

        // Capturar el contenido de la vista
        ob_start();
        require $vistaPath;
        $contenido = ob_get_clean();

        // Capturar el HTML final (layout + vista)
        ob_start();
        require $layoutPath;
        $html = ob_get_clean();

        // 3) Guardar en cache si corresponde
        if ($cacheTtl > 0 && $cacheFile !== null) {
            $this->guardarEnCache($cacheFile, $html);
        }

        echo $html;
    }

    /**
     * Calcula la ruta del archivo de cache para una vista/datos dados.
     * Devuelve null si los datos no son serializables (=> no cachear).
     */
    private function getViewCacheFile(string $vista, string $layout, array $datos): ?string
    {
        try {
            $vistaPath  = __DIR__ . '/../Views/' . $vista . '.php';
            $layoutPath = __DIR__ . '/../Views/layouts/' . $layout . '.php';

            $mtimeVista  = @filemtime($vistaPath) ?: 0;
            $mtimeLayout = @filemtime($layoutPath) ?: 0;

            // Aislamiento por usuario: 'guest' o el ID de sesión
            $contexto = $_SESSION['usuario_id'] ?? 'guest';

            $clave = md5(
                $vista . '|' . $layout . '|' .
                $mtimeVista . '|' . $mtimeLayout . '|' .
                serialize($datos) . '|' . $contexto
            );
        } catch (\Throwable $e) {
            return null;
        }

        $dir = __DIR__ . '/../../storage/cache/views';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        return $dir . '/' . $clave . '.html';
    }

    /**
     * Escribe el HTML en el archivo de cache (silencioso si falla).
     */
    private function guardarEnCache(?string $cacheFile, string $html): void
    {
        if ($cacheFile === null) {
            return;
        }
        @file_put_contents($cacheFile, $html, LOCK_EX);
    }

    /**
     * Elimina todos los archivos de cache de vistas.
     * Útil tras un deploy o cambio grande.
     *
     * @return int Número de archivos eliminados.
     */
    public static function clearViewCache(): int
    {
        $dir = __DIR__ . '/../../storage/cache/views';
        if (!is_dir($dir)) {
            return 0;
        }

        $count = 0;
        foreach (glob($dir . '/*.html') ?: [] as $file) {
            if (@unlink($file)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Redirige a una URL.
     */
    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit();
    }

    /**
     * Devuelve una respuesta JSON.
     */
    protected function json($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }

    /**
     * Verifica que el usuario esté logueado. Si no, redirige al login.
     */
    protected function requireLogin(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect(base_url('/'));
        }
    }

    /**
     * ¿El usuario logueado tiene al menos el rol indicado?
     */
    protected function hasRole(string $rolMinimo): bool
    {
        $rolActual = $_SESSION['usuario_rol'] ?? null;
        return Roles::puede($rolActual, $rolMinimo);
    }

    /**
     * Exige un rol mínimo (usuario, moderador o admin).
     */
    protected function requireRole(string $rolMinimo): void
    {
        $this->requireLogin();
        if (!$this->hasRole($rolMinimo)) {
            http_response_code(403);
            require __DIR__ . '/../../403.php';
            exit();
        }
    }

    /**
     * Atajo: exige rol admin (compatibilidad con código existente).
     */
    protected function requireAdmin(): void
    {
        $this->requireRole(Roles::ADMIN);
    }

    /**
     * Atajo: exige rol moderador o superior.
     */
    protected function requireModerator(): void
    {
        $this->requireRole(Roles::MODERADOR);
    }
}