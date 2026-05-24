<?php

require_once __DIR__ . '/Model.php';

class Domicilio extends Model
{
    public function getAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM domicilios ORDER BY direccion');
        return $stmt->fetchAll();
    }
}
