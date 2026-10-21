<?php
namespace Tests\Integration;

use App\Core\Database;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * Tests de integración de la clase Database.
 * Requieren MySQL corriendo y las credenciales del .env correctas.
 *
 * Sirven como smoke test antes de desplegar a producción:
 * si esto falla, la app no arrancará en el servidor.
 */
class DatabaseTest extends TestCase
{
    public function test_conexion_devuelve_pdo(): void
    {
        $pdo = Database::getConnection();
        $this->assertInstanceOf(PDO::class, $pdo);
    }

    public function test_select_1_funciona(): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query('SELECT 1 AS uno');
        $fila = $stmt->fetch();

        $this->assertSame(1, (int) $fila['uno']);
    }

    public function test_charset_es_utf8mb4(): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SHOW VARIABLES LIKE 'character_set_connection'");
        $fila = $stmt->fetch();

        $this->assertSame('utf8mb4', $fila['Value']);
    }

    public function test_err_mode_es_exception(): void
    {
        $pdo = Database::getConnection();
        $this->assertSame(PDO::ERRMODE_EXCEPTION, $pdo->getAttribute(PDO::ATTR_ERRMODE));
    }

    public function test_emulate_prepares_desactivado(): void
    {
        $pdo = Database::getConnection();
        $this->assertFalse((bool) $pdo->getAttribute(PDO::ATTR_EMULATE_PREPARES));
    }
}