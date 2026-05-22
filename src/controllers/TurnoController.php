<?php

require_once __DIR__ . '/../models/Turno.php';

class TurnoController
{
    private Turno $turnoModel;

    public function __construct(PDO $pdo)
    {
        $this->turnoModel = new Turno($pdo);
    }

    public function index(): array
    {
        return $this->turnoModel->getAll();
    }
}
