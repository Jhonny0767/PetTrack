<?php
namespace Tests\Unit;

use App\Core\Router;
use PHPUnit\Framework\TestCase;

/**
 * Tests del Router.
 * Verifican la lógica de matching de rutas sin necesidad de BD ni navegador.
 */
class RouterTest extends TestCase
{
    /**
     * Helper: invoca el método privado match() usando reflexión.
     */
    private function invocarMatch(Router $router, string $rutaRegistrada, string $uriEntrante)
    {
        $reflexion = new \ReflectionClass($router);
        $metodo = $reflexion->getMethod('match');
        $metodo->setAccessible(true);
        return $metodo->invoke($router, $rutaRegistrada, $uriEntrante);
    }

    public function test_ruta_estatica_hace_match(): void
    {
        $router = new Router();
        $resultado = $this->invocarMatch($router, '/dashboard', '/dashboard');

        $this->assertIsArray($resultado);
        $this->assertEmpty($resultado, 'Una ruta estática no debe devolver parámetros');
    }

    public function test_ruta_dinamica_captura_el_id(): void
    {
        $router = new Router();
        $resultado = $this->invocarMatch($router, '/mascotas/editar/{id}', '/mascotas/editar/43');

        $this->assertIsArray($resultado);
        $this->assertEquals(['43'], $resultado, 'Debe capturar el id de la URL');
    }

    public function test_ruta_dinamica_con_multiples_params(): void
    {
        $router = new Router();
        $resultado = $this->invocarMatch($router, '/usuario/{uid}/mascota/{mid}', '/usuario/5/mascota/99');

        $this->assertEquals(['5', '99'], $resultado);
    }

    public function test_ruta_no_hace_match_con_url_distinta(): void
    {
        $router = new Router();
        $resultado = $this->invocarMatch($router, '/dashboard', '/perfil');

        $this->assertFalse($resultado, 'URLs distintas no deben coincidir');
    }

    public function test_ruta_no_hace_match_parcial(): void
    {
        $router = new Router();
        $resultado = $this->invocarMatch($router, '/dashboard', '/dashboard/extra');

        $this->assertFalse($resultado, 'No debe hacer match parcial');
    }

    public function test_ruta_registrada_se_almacena(): void
    {
        $router = new Router();
        $router->get('/dashboard', 'HomeController@index');
        $router->post('/login', 'AuthController@login');

        $reflexion = new \ReflectionClass($router);
        $propiedad = $reflexion->getProperty('rutas');
        $propiedad->setAccessible(true);
        $rutas = $propiedad->getValue($router);

        $this->assertCount(2, $rutas);
        $this->assertEquals('/dashboard', $rutas[0]['uri']);
        $this->assertEquals('GET', $rutas[0]['metodo']);
        $this->assertEquals('/login', $rutas[1]['uri']);
        $this->assertEquals('POST', $rutas[1]['metodo']);
    }
}