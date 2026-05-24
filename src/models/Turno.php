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

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO turnos (fecha, hora_inicio, hora_fin, estado, enfermero_id, domicilio_id, coordinador_id)
             VALUES (:fecha, :hora_inicio, :hora_fin, :estado, :enfermero_id, :domicilio_id, :coordinador_id)'
        );

        $stmt->execute([
            'fecha' => $data['fecha'],
            'hora_inicio' => $data['hora_inicio'],
            'hora_fin' => $data['hora_fin'],
            'estado' => $data['estado'],
            'enfermero_id' => $data['enfermero_id'],
            'domicilio_id' => $data['domicilio_id'],
            'coordinador_id' => $data['coordinador_id'],
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE turnos
             SET fecha = :fecha,
                 hora_inicio = :hora_inicio,
                 hora_fin = :hora_fin,
                 estado = :estado,
                 enfermero_id = :enfermero_id,
                 domicilio_id = :domicilio_id
             WHERE id = :id'
        );

        $stmt->execute([
            'fecha' => $data['fecha'],
            'hora_inicio' => $data['hora_inicio'],
            'hora_fin' => $data['hora_fin'],
            'estado' => $data['estado'],
            'enfermero_id' => $data['enfermero_id'],
            'domicilio_id' => $data['domicilio_id'],
            'id' => $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM turnos WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
