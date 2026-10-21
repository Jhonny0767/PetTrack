<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use PDO;

class Notificacion extends Model
{
    /**
     * Crea una nueva notificación para un usuario.
     */
    public static function crear(int $usuarioId, string $tipo, string $titulo, string $mensaje, ?string $url = null): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, url) 
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$usuarioId, $tipo, $titulo, $mensaje, $url]);
    }

    /**
     * Obtiene las notificaciones de un usuario.
     * Si $soloNoLeidas es true, solo trae las que tienen leida = 0.
     */
    public static function paraUsuario(int $usuarioId, bool $soloNoLeidas = false, int $limit = 20): array
    {
        $db = Database::getConnection();
        $sql = "SELECT * FROM notificaciones WHERE usuario_id = ?";
        
        if ($soloNoLeidas) {
            $sql .= " AND leida = 0";
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT " . (int)$limit;
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cuenta cuántas notificaciones no leídas tiene el usuario.
     */
    public static function contarNoLeidas(int $usuarioId): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM notificaciones WHERE usuario_id = ? AND leida = 0");
        $stmt->execute([$usuarioId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Marca una notificación específica como leída.
     */
    public static function marcarLeida(int $id, int $usuarioId): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE notificaciones SET leida = 1 WHERE id = ? AND usuario_id = ?");
        return $stmt->execute([$id, $usuarioId]);
    }

    /**
     * Marca TODAS las notificaciones de un usuario como leídas.
     */
    public static function marcarTodasLeidas(int $usuarioId): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE notificaciones SET leida = 1 WHERE usuario_id = ? AND leida = 0");
        return $stmt->execute([$usuarioId]);
    }
}