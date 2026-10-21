<?php
namespace App\Core;

use PDO;
use PDOException;

/**
 * Clase Database
 * Maneja la conexión a la base de datos usando PDO.
 * Patrón Singleton: una sola instancia para toda la aplicación.
 */
class Database
{
    private static ?PDO $instance = null;

    /**
     * Devuelve la conexión PDO (creándola si no existe).
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $host = env('DB_HOST', 'localhost');
            $user = env('DB_USER', 'root');
            $pass = env('DB_PASS', '');
            $name = env('DB_NAME', 'login_register_db');

            $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";

            try {
                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                http_response_code(500);
                exit("Error de conexión a la base de datos: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}