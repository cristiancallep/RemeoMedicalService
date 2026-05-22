<?php

require_once __DIR__ . '/Model.php';

class Historial extends Model
{
    public function logChange(int $turnoId, int $usuarioId, string $cambio): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO historial (turno_id, usuario_id, cambio, fecha_cambio)
             VALUES (:turno_id, :usuario_id, :cambio, NOW())'
        );

        return $stmt->execute([
            'turno_id' => $turnoId,
            'usuario_id' => $usuarioId,
            'cambio' => $cambio,
        ]);
    }
}
