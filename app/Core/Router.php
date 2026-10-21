<?php
namespace App\Core;

/**
 * Clase Router
 * Mapea las URLs a controladores y métodos.
 * Soporta rutas estáticas y dinámicas con parámetros.
 */
class Router
{
    private array $rutas = [];

    /**
     * Registra una ruta GET.
     */
    public function get(string $uri, string $accion): void
    {
        $this->registrar('GET', $uri, $accion);
    }

    /**
     * Registra una ruta POST.
     */
    public function post(string $uri, string $accion): void
    {
        $this->registrar('POST', $uri, $accion);
    }

    /**
     * Registra una ruta para cualquier método.
     */
    private function registrar(string $metodo, string $uri, string $accion): void
    {
        // Normalizar URI: quitar barra final excepto en "/"
        $uri = rtrim($uri, '/') ?: '/';
        $this->rutas[] = [
            'metodo' => $metodo,
            'uri'    => $uri,
            'accion' => $accion,
        ];
    }

    /**
     * Ejecuta el router: busca la ruta que coincida y llama al controlador.
     */
    public function dispatch(string $uri, string $metodo): void
    {
        // Normalizar URI entrante
        $uri = rtrim($uri, '/') ?: '/';
        // Quitar query string si la hay
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }

        foreach ($this->rutas as $ruta) {
            if ($ruta['metodo'] !== $metodo) {
                continue;
            }

            $params = $this->match($ruta['uri'], $uri);
            if ($params !== false) {
                $this->llamarControlador($ruta['accion'], $params);
                return;
            }
        }

        // No se encontró ninguna ruta
        $this->notFound();
    }

    /**
     * Compara una ruta registrada con la URI entrante.
     * Devuelve los parámetros capturados, o false si no coincide.
     */
    private function match(string $rutaRegistrada, string $uriEntrante)
    {
        // Convertir {param} en regex de captura
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '([^/]+)', $rutaRegistrada);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $uriEntrante, $matches)) {
            array_shift($matches); // quitar el match completo
            return $matches;
        }

        return false;
    }

    /**
     * Instancia el controlador y llama al método correspondiente.
     * Formato de $accion: "NombreControlador@metodo"
     */
    private function llamarControlador(string $accion, array $params): void
    {
        [$controladorNombre, $metodo] = explode('@', $accion);

        $claseCompleta = "App\\Controllers\\{$controladorNombre}";

        if (!class_exists($claseCompleta)) {
            http_response_code(500);
            exit("Controlador no encontrado: {$controladorNombre}");
        }

        $controlador = new $claseCompleta();

        if (!method_exists($controlador, $metodo)) {
            http_response_code(500);
            exit("Método no encontrado: {$controladorNombre}@{$metodo}");
        }

        // Llamar al método con los parámetros capturados
        call_user_func_array([$controlador, $metodo], $params);
    }

    /**
     * Muestra la página 404.
     */
    private function notFound(): void
    {
        http_response_code(404);
        $path404 = __DIR__ . '/../../404.php';
        if (file_exists($path404)) {
            require $path404;
        } else {
            echo "<h1>404 - Página no encontrada</h1>";
        }
        exit();
    }
}