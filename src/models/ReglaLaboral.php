<?php

require_once __DIR__ . '/Model.php';

class ReglaLaboral extends Model
{
    public function getActiveRule(): ?array
    {
        $stmt = $this->pdo->query('SELECT * FROM reglas_laborales WHERE activo = 1 ORDER BY id DESC LIMIT 1');
        $rule = $stmt->fetch();
        return $rule ?: null;
    }
}
