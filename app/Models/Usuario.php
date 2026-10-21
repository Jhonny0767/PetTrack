<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

/**
 * Modelo Usuario
 * Gestiona las operaciones sobre la tabla usuarios.
 */
class Usuario extends Model
{
    protected string $tabla = 'usuarios';

    /**
     * Busca un usuario por su correo.
     */
    public function porCorreo(string $correo): ?array
    {
        return $this->fetchOne(
            "SELECT id, nombre_completo, correo, usuario, password_, rol, foto
             FROM usuarios WHERE correo = ?",
            [$correo]
        );
    }

    /**
     * Busca un usuario por su nombre de usuario.
     */
    public function porNombreUsuario(string $usuario): ?array
    {
        return $this->fetchOne(
            "SELECT id FROM usuarios WHERE usuario = ?",
            [$usuario]
        );
    }

    /**
     * Busca un usuario por ID.
     */
    public function porId(int $id): ?array
    {
        return $this->fetchOne(
            "SELECT id, nombre_completo, correo, usuario, rol, foto
             FROM usuarios WHERE id = ?",
            [$id]
        );
    }

    /**
     * Crea un nuevo usuario y devuelve su ID.
     */
    public function crear(string $nombre, string $correo, string $usuario, string $passwordHash, ?string $foto = null): int
    {
        return $this->insert([
            'nombre_completo' => $nombre,
            'correo'          => $correo,
            'usuario'         => $usuario,
            'password_'       => $passwordHash,
            'foto'            => $foto,
            'rol'             => 'usuario',
        ]);
    }

    /**
     * Verifica si un correo ya está registrado.
     */
    public function correoExiste(string $correo, int $exceptoId = 0): bool
    {
        if ($exceptoId > 0) {
            $total = $this->count('correo = ? AND id != ?', [$correo, $exceptoId]);
        } else {
            $total = $this->count('correo = ?', [$correo]);
        }
        return $total > 0;
    }

    /**
     * Verifica si un nombre de usuario ya está registrado.
     */
    public function usuarioExiste(string $usuario, int $exceptoId = 0): bool
    {
        if ($exceptoId > 0) {
            $total = $this->count('usuario = ? AND id != ?', [$usuario, $exceptoId]);
        } else {
            $total = $this->count('usuario = ?', [$usuario]);
        }
        return $total > 0;
    }

    /**
     * Devuelve TODOS los usuarios con sus mascotas (con GROUP_CONCAT).
     * Usado en el panel admin.
     */
    public function listarConMascotas(string $buscar = ''): array
    {
        $sql = "SELECT
                    u.id, u.nombre_completo, u.correo, u.usuario, u.foto, u.rol,
                    GROUP_CONCAT(m.nombre SEPARATOR '||') AS mascotas_nombres,
                    GROUP_CONCAT(m.clase SEPARATOR '||') AS mascotas_clases,
                    GROUP_CONCAT(m.cirugias SEPARATOR '||') AS mascotas_cirugias,
                    GROUP_CONCAT(m.foto SEPARATOR '||') AS mascotas_fotos,
                    (SELECT GROUP_CONCAT(
                        CONCAT(v.nombre, '::', DATE_FORMAT(v.fecha_aplicacion, '%d/%m/%Y'))
                        SEPARATOR '||'
                     )
                     FROM vacunas v
                     INNER JOIN mascotas m2 ON m2.id = v.mascota_id
                     WHERE m2.usuario_id = u.id
                       AND m2.id = (SELECT MIN(id) FROM mascotas WHERE usuario_id = u.id)
                    ) AS primera_mascota_vacunas,
                    (SELECT COUNT(*)
                     FROM visitas_vet vv
                     INNER JOIN mascotas m3 ON m3.id = vv.mascota_id
                     WHERE m3.usuario_id = u.id
                    ) AS total_visitas
                FROM usuarios u
                LEFT JOIN mascotas m ON m.usuario_id = u.id";

        $params = [];

        if ($buscar !== '') {
            $sql .= " WHERE u.nombre_completo LIKE ?
                      OR u.correo LIKE ?
                      OR u.usuario LIKE ?
                      OR m.nombre LIKE ?";
            $like = "%{$buscar}%";
            $params = [$like, $like, $like, $like];
        }

        $sql .= " GROUP BY u.id";

        return $this->fetchAll($sql, $params);
    }

    /**
     * Actualiza el rol de un usuario.
     */
    public function actualizarRol(int $id, string $rol): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE usuarios SET rol = ? WHERE id = ?");
        return $stmt->execute([$rol, $id]);
    }
}