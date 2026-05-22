<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/Auth.php';

class AuthController
{
    private User $userModel;

    public function __construct(PDO $pdo)
    {
        $this->userModel = new User($pdo);
    }

    /**
     * Autentica un usuario con email y contraseña.
     * Retorna el usuario si la autenticación es exitosa, null si falla.
     */
    public function login(string $email, string $password): ?array
    {
        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            return null;
        }

        if (!password_verify($password, $user['password_hash'])) {
            return null;
        }

        // Autenticación exitosa
        Auth::login($user);
        return $user;
    }

    /**
     * Realiza logout del usuario.
     */
    public function logout(): void
    {
        Auth::logout();
    }

    /**
     * Verifica si el usuario está autenticado.
     */
    public function isAuthenticated(): bool
    {
        return Auth::isAuthenticated();
    }
}
