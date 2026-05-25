<?php
require_once dirname(__DIR__) . '/models/ReglaLaboral.php';

class ReglaLaboralController {
        public function activate($id) {
            return $this->model->activate($id);
        }
    private $model;

    public function __construct($pdo) {
        $this->model = new ReglaLaboral($pdo);
    }

    public function index() {
        return $this->model->getAll();
    }

    public function store($data) {
        $errors = [];
        if (empty($data['horas_maximas']) || !is_numeric($data['horas_maximas']) || $data['horas_maximas'] <= 0) {
            $errors[] = 'Las horas máximas deben ser un número positivo.';
        }
        if (empty($data['descanso_minimo']) || !is_numeric($data['descanso_minimo']) || $data['descanso_minimo'] < 0) {
            $errors[] = 'El descanso mínimo debe ser un número mayor o igual a 0.';
        }
        if ($errors) return $errors;
        $this->model->create($data);
        return [];
    }

    public function getById($id) {
        return $this->model->getById($id);
    }

    public function update($id, $data) {
        $errors = [];
        if (empty($data['horas_maximas']) || !is_numeric($data['horas_maximas']) || $data['horas_maximas'] <= 0) {
            $errors[] = 'Las horas máximas deben ser un número positivo.';
        }
        if (empty($data['descanso_minimo']) || !is_numeric($data['descanso_minimo']) || $data['descanso_minimo'] < 0) {
            $errors[] = 'El descanso mínimo debe ser un número mayor o igual a 0.';
        }
        if ($errors) return $errors;
        $this->model->update($id, $data);
        return [];
    }

    public function deactivate($id) {
    return $this->model->deactivate($id);
}
}