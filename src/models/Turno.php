<?php

require_once __DIR__ . '/Model.php';

class Turno extends Model
{
    public function getAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT t.*, u.nombre AS enfermero_nombre, d.direccion AS domicilio_direccion
             FROM turnos t
             LEFT JOIN usuarios u ON t.enfermero_id = u.id
             LEFT JOIN domicilios d ON t.domicilio_id = d.id
             ORDER BY t.fecha, t.hora_inicio'
        );
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM turnos WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $turno = $stmt->fetch();
        return $turno ?: null;
    }
}
