<?php
namespace App\Core;

/**
 * Clase Roles
 * Define los roles disponibles y su jerarquía de permisos.
 *
 * Jerarquía (quien puede más, puede todo lo de abajo):
 *   admin > moderador > usuario
 */
final class Roles
{
    public const USUARIO   = 'usuario';
    public const MODERADOR = 'moderador';
    public const ADMIN     = 'admin';

    /**
     * Niveles numéricos: a mayor número, más permisos.
     */
    private const NIVELES = [
        self::USUARIO   => 1,
        self::MODERADOR => 2,
        self::ADMIN     => 3,
    ];

    /**
     * Devuelve todos los roles válidos.
     *
     * @return string[]
     */
    public static function todos(): array
    {
        return array_keys(self::NIVELES);
    }

    /**
     * ¿El rol existe?
     */
    public static function existe(?string $rol): bool
    {
        return $rol !== null && isset(self::NIVELES[$rol]);
    }

    /**
     * Nivel numérico de un rol (0 si no existe).
     */
    public static function nivel(?string $rol): int
    {
        return self::NIVELES[$rol] ?? 0;
    }

    /**
     * ¿El rol del usuario alcanza el mínimo requerido?
     * Admin (3) cumple cualquier mínimo. Moderador (2) cumple usuario y moderador.
     */
    public static function puede(?string $rolUsuario, string $rolMinimo): bool
    {
        return self::nivel($rolUsuario) >= self::nivel($rolMinimo);
    }

    /**
     * Nombre amigable para mostrar en la interfaz.
     */
    public static function etiqueta(?string $rol): string
    {
        return match ($rol) {
            self::ADMIN     => 'Administrador',
            self::MODERADOR => 'Moderador',
            self::USUARIO   => 'Usuario',
            default         => 'Desconocido',
        };
    }
}