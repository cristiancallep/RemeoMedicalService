<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/Auth.php';

class UserController
{
    private User $userModel;

    public function __construct(PDO $pdo)
    {
        // Instancia el modelo User 
        $this->userModel = new User($pdo);
    }

    // Mostrar todos los usuarios
    public function index(): array
    {
        // Llama al método getAll() del modelo User
        return $this->userModel->getAll();
    }

    // Guardar nuevo usuario
    public function store(array $data): array
    {
        $errors = [];
        if (empty($data['nombre'])) $errors[] = 'El nombre es obligatorio.';
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido.';
        if (empty($data['rol'])) $errors[] = 'El rol es obligatorio.';
        if (empty($data['password']) || strlen($data['password']) < 6) $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
        if ($this->userModel->findByEmail($data['email'])) $errors[] = 'El email ya está registrado.';
        if ($errors) return $errors;
        $this->userModel->create($data);
        return [];
    }

    // Buscar usuario por ID
    public function find($id)
    {
        return $this->userModel->findById($id);
    }

    // Actualizar usuario
    public function update($id, array $data): array
    {
        $errors = [];
        if (empty($data['nombre'])) $errors[] = 'El nombre es obligatorio.';
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido.';
        if (empty($data['rol'])) $errors[] = 'El rol es obligatorio.';
        if ($errors) return $errors;
        $this->userModel->update($id, $data);
        return [];
    }

    // Eliminar usuario
    public function delete($id): void
    {
        $this->userModel->delete($id);
    }
}
