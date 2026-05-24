<?php

require_once __DIR__ . '/../models/Turno.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Domicilio.php';

class TurnoController
{
    private Turno $turnoModel;
    private User $userModel;
    private Domicilio $domicilioModel;
    private array $allowedStates = ['Pendiente', 'Asignado', 'Completado', 'Cancelado'];

    public function __construct(PDO $pdo)
    {
        $this->turnoModel = new Turno($pdo);
        $this->userModel = new User($pdo);
        $this->domicilioModel = new Domicilio($pdo);
    }

    public function index(): array
    {
        return $this->turnoModel->getAll();
    }

    public function find($id): ?array
    {
        if (empty($id) || !ctype_digit((string)$id)) {
            return null;
        }

        return $this->turnoModel->findById((int)$id);
    }

    public function getNurses(): array
    {
        return $this->userModel->findAllByRole('enfermero');
    }

    public function getDomicilios(): array
    {
        return $this->domicilioModel->getAll();
    }

    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['fecha'])) {
            $errors[] = 'La fecha es obligatoria.';
        }

        if (empty($data['hora_inicio'])) {
            $errors[] = 'La hora de inicio es obligatoria.';
        }

        if (empty($data['hora_fin'])) {
            $errors[] = 'La hora de fin es obligatoria.';
        }

        if (!empty($data['hora_inicio']) && !empty($data['hora_fin']) && $data['hora_inicio'] >= $data['hora_fin']) {
            $errors[] = 'La hora de inicio debe ser anterior a la hora de fin.';
        }

        if (!empty($data['enfermero_id']) && !ctype_digit((string)$data['enfermero_id'])) {
            $errors[] = 'El enfermero seleccionado no es válido.';
        }

        if (!empty($data['domicilio_id']) && !ctype_digit((string)$data['domicilio_id'])) {
            $errors[] = 'El domicilio seleccionado no es válido.';
        }

        if (!empty($data['estado']) && !in_array($data['estado'], $this->allowedStates, true)) {
            $errors[] = 'El estado seleccionado no es válido.';
        }

        return $errors;
    }

    public function store(array $data, int $coordinadorId): int
    {
        $enfermeroId = !empty($data['enfermero_id']) ? (int)$data['enfermero_id'] : null;
        $domicilioId = !empty($data['domicilio_id']) ? (int)$data['domicilio_id'] : null;
        $estado = $this->normalizeState($data['estado'] ?? 'Pendiente', $enfermeroId);

        return $this->turnoModel->create([
            'fecha' => $data['fecha'],
            'hora_inicio' => $data['hora_inicio'],
            'hora_fin' => $data['hora_fin'],
            'estado' => $estado,
            'enfermero_id' => $enfermeroId,
            'domicilio_id' => $domicilioId,
            'coordinador_id' => $coordinadorId,
        ]);
    }

    public function update($id, array $data): void
    {
        $enfermeroId = !empty($data['enfermero_id']) ? (int)$data['enfermero_id'] : null;
        $domicilioId = !empty($data['domicilio_id']) ? (int)$data['domicilio_id'] : null;
        $estado = $this->normalizeState($data['estado'] ?? 'Pendiente', $enfermeroId);

        $this->turnoModel->update((int)$id, [
            'fecha' => $data['fecha'],
            'hora_inicio' => $data['hora_inicio'],
            'hora_fin' => $data['hora_fin'],
            'estado' => $estado,
            'enfermero_id' => $enfermeroId,
            'domicilio_id' => $domicilioId,
        ]);
    }

    public function delete($id): void
    {
        if (empty($id) || !ctype_digit((string)$id)) {
            return;
        }

        $this->turnoModel->delete((int)$id);
    }

    private function normalizeState(string $estado, ?int $enfermeroId): string
    {
        if ($enfermeroId !== null && $estado !== 'Completado' && $estado !== 'Cancelado') {
            return 'Asignado';
        }

        return in_array($estado, $this->allowedStates, true) ? $estado : 'Pendiente';
    }
}
