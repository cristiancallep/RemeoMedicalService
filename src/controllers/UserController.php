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
    public function store(array $data): string
    {
        // Validar email único
        if ($this->userModel->findByEmail($data['email'])) {
            return 'duplicate';
        }
        $this->userModel->create($data);
        return 'success';
    }

    // Buscar usuario por ID
    public function find($id)
    {
        return $this->userModel->findById($id);
    }

    // Actualizar usuario
    public function update($id, array $data): void
    {
        $this->userModel->update($id, $data);
    }

    // Eliminar usuario
    public function delete($id): void
    {
        $this->userModel->delete($id);
    }
}
