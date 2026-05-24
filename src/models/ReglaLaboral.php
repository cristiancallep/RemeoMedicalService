<?php

require_once __DIR__ . '/Model.php';

class ReglaLaboral extends Model
{
    // Activar regla laboral
    public function activate($id) {
        $stmt = $this->pdo->prepare("UPDATE reglas_laborales SET activo = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
    public function getActiveRule(): ?array
    {
        $stmt = $this->pdo->query('SELECT * FROM reglas_laborales WHERE activo = 1 ORDER BY id DESC LIMIT 1');
        $rule = $stmt->fetch();
        return $rule ?: null;
    }

    // Obtener todas las reglas laborales
    public function getAll($table = 'reglas_laborales') {
        $stmt = $this->pdo->prepare("SELECT * FROM $table ORDER BY id");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Crear nueva regla laboral
    public function create($table, array $data) {
        $stmt = $this->pdo->prepare("INSERT INTO $table (horas_maximas, descanso_minimo, activo) VALUES (:horas_maximas, :descanso_minimo, 1)");
        return $stmt->execute([
            'horas_maximas' => $data['horas_maximas'],
            'descanso_minimo' => $data['descanso_minimo']
        ]);
    }

    // Buscar regla laboral por ID
    public function getById($table, $id) {
        $stmt = $this->pdo->prepare("SELECT * FROM $table WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // Actualizar regla laboral
    public function update($table, $id, array $data) {
        $stmt = $this->pdo->prepare("UPDATE $table SET horas_maximas = :horas_maximas, descanso_minimo = :descanso_minimo WHERE id = :id");
        return $stmt->execute([
            'horas_maximas' => $data['horas_maximas'],
            'descanso_minimo' => $data['descanso_minimo'],
            'id' => $id
        ]);
    }

    // Eliminar regla laboral (opcional, si se quiere eliminar físicamente)
    public function delete($table, $id) {
        $stmt = $this->pdo->prepare("DELETE FROM $table WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function deactivate($id) {
        $stmt = $this->pdo->prepare("UPDATE reglas_laborales SET activo = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
