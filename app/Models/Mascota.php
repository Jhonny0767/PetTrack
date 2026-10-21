<?php
namespace App\Models;

use App\Core\Model;

/**
 * Modelo Mascota
 * Gestiona mascotas, vacunas y visitas al veterinario.
 */
class Mascota extends Model
{
    protected string $tabla = 'mascotas';

    /**
     * Devuelve todas las mascotas de un usuario, con sus vacunas y visitas.
     */
    public function porUsuarioConDetalles(int $usuario_id): array
    {
        $mascotas = $this->fetchAll(
            "SELECT id, nombre, clase, cirugias, foto, raza, edad, peso, alergias
             FROM mascotas
             WHERE usuario_id = ?
             ORDER BY id ASC",
            [$usuario_id]
        );

        foreach ($mascotas as &$m) {
            $m['vacunas']  = $this->vacunasDe($m['id']);
            $m['visitas']  = $this->visitasDe($m['id']);
        }

        return $mascotas;
    }

    /**
     * Devuelve una mascota por ID (sin validar usuario).
     */
    public function porId(int $id): ?array
    {
        return $this->fetchOne(
            "SELECT id, nombre, clase, cirugias, foto, raza, edad, peso, alergias, usuario_id
             FROM mascotas WHERE id = ?",
            [$id]
        );
    }

    /**
     * Devuelve una mascota por ID solo si pertenece al usuario indicado.
     */
    public function porIdYUsuario(int $id, int $usuario_id): ?array
    {
        return $this->fetchOne(
            "SELECT id, nombre, clase, cirugias, foto, raza, edad, peso, alergias
             FROM mascotas WHERE id = ? AND usuario_id = ?",
            [$id, $usuario_id]
        );
    }

    /**
     * Vacunas de una mascota.
     */
    public function vacunasDe(int $mascota_id): array
    {
        return $this->fetchAll(
            "SELECT id, nombre, fecha_aplicacion 
             FROM vacunas 
             WHERE mascota_id = ? 
             ORDER BY fecha_aplicacion DESC",
            [$mascota_id]
        );
    }

    /**
     * Visitas al veterinario de una mascota.
     */
    public function visitasDe(int $mascota_id): array
    {
        return $this->fetchAll(
            "SELECT id, fecha, motivo, veterinario 
             FROM visitas_vet 
             WHERE mascota_id = ? 
             ORDER BY fecha DESC",
            [$mascota_id]
        );
    }

    /**
     * Cuenta mascotas de un usuario.
     */
    public function contarPorUsuario(int $usuario_id): int
    {
        return $this->count('usuario_id = ?', [$usuario_id]);
    }
}