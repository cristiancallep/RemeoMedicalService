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
        return $this->model->getAll('reglas_laborales');
    }

    public function store($data) {
        return $this->model->create('reglas_laborales', $data);
    }

    public function getById($id) {
        return $this->model->getById('reglas_laborales', $id);
    }

    public function update($id, $data) {
        return $this->model->update('reglas_laborales', $id, $data);
    }

    public function deactivate($id) {
    return $this->model->deactivate($id);
}
}