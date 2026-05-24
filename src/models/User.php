<?php

require_once __DIR__ . '/Model.php';

class User extends Model
{
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM usuarios WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findAllByRole(string $role): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM usuarios WHERE rol = :rol ORDER BY nombre');
        $stmt->execute(['rol' => $role]);
        return $stmt->fetchAll();
    }
    //Obtiene todos los usuarios de la base de datos (retorna un array)
    public function getAll(): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM usuarios ORDER BY id');
        $stmt->execute();
        //Retorna todos los usuarios en un array
        return $stmt->fetchAll();
    }

    // Crear usuario
    public function create(array $data): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO usuarios (nombre, email, rol, password_hash) VALUES (:nombre, :email, :rol, :password_hash)');
        $stmt->execute([
            'nombre' => $data['nombre'],
            'email' => $data['email'],
            'rol' => $data['rol'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT)
        ]);
    }

    // Buscar usuario por ID
    public function findById($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM usuarios WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // Actualizar usuario
    public function update($id, array $data): void
    {
        if (!empty($data['password'])) {
            $stmt = $this->pdo->prepare('UPDATE usuarios SET nombre = :nombre, email = :email, rol = :rol, password_hash = :password_hash WHERE id = :id');
            $stmt->execute([
                'nombre' => $data['nombre'],
                'email' => $data['email'],
                'rol' => $data['rol'],
                'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
                'id' => $id
            ]);
        } else {
            $stmt = $this->pdo->prepare('UPDATE usuarios SET nombre = :nombre, email = :email, rol = :rol WHERE id = :id');
            $stmt->execute([
                'nombre' => $data['nombre'],
                'email' => $data['email'],
                'rol' => $data['rol'],
                'id' => $id
            ]);
        }
    }

    // Eliminar usuario
    public function delete($id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM usuarios WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
