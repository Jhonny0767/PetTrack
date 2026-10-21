<?php
namespace App\Core;

use PDO;

/**
 * Clase Model
 * Clase base para todos los modelos.
 * Proporciona métodos genéricos para trabajar con la base de datos.
 */
abstract class Model
{
    protected PDO $db;
    protected string $tabla = '';

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Ejecuta una consulta preparada y devuelve el statement.
     */
    protected function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Devuelve todas las filas de una consulta.
     */
    protected function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Devuelve la primera fila de una consulta.
     */
    protected function fetchOne(string $sql, array $params = []): ?array
    {
        $row = $this->query($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    /**
     * Devuelve un solo valor (primera columna de la primera fila).
     */
    protected function fetchColumn(string $sql, array $params = [])
    {
        return $this->query($sql, $params)->fetchColumn();
    }

    /**
     * Ejecuta un INSERT/UPDATE/DELETE y devuelve el número de filas afectadas.
     */
    protected function execute(string $sql, array $params = []): int
    {
        return $this->query($sql, $params)->rowCount();
    }

    /**
     * Inserta un registro en la tabla del modelo y devuelve el ID.
     */
    protected function insert(array $datos): int
    {
        $columnas = implode(', ', array_keys($datos));
        $marcadores = implode(', ', array_fill(0, count($datos), '?'));

        $sql = "INSERT INTO {$this->tabla} ({$columnas}) VALUES ({$marcadores})";
        $this->execute($sql, array_values($datos));

        return (int) $this->db->lastInsertId();
    }

    /**
     * Actualiza un registro por ID.
     */
    protected function update(int $id, array $datos): int
    {
        $sets = [];
        foreach (array_keys($datos) as $col) {
            $sets[] = "{$col} = ?";
        }
        $sets = implode(', ', $sets);

        $sql = "UPDATE {$this->tabla} SET {$sets} WHERE id = ?";
        $params = array_values($datos);
        $params[] = $id;

        return $this->execute($sql, $params);
    }

    /**
     * Elimina un registro por ID.
     */
    protected function delete(int $id): int
    {
        return $this->execute("DELETE FROM {$this->tabla} WHERE id = ?", [$id]);
    }

    /**
     * Cuenta filas según una condición.
     */
    protected function count(string $where = '1', array $params = []): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->tabla} WHERE {$where}";
        return (int) $this->fetchColumn($sql, $params);
    }
}