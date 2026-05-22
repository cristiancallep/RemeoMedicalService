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
}
