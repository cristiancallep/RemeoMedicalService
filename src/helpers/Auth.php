<?php

class Auth
{
    /**
     * Verifica si el usuario está autenticado.
     */
    public static function isAuthenticated(): bool
    {
        return isset($_SESSION['user_id']) && isset($_SESSION['user_email']);
    }

    /**
     * Retorna el usuario actual de la sesión.
     */
    public static function getCurrentUser(): ?array
    {
        if (!self::isAuthenticated()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'],
            'nombre' => $_SESSION['user_nombre'],
            'rol' => $_SESSION['user_rol'],
        ];
    }

    /**
     * Retorna el rol del usuario actual.
     */
    public static function getRole(): ?string
    {
        return $_SESSION['user_rol'] ?? null;
    }

    /**
     * Verifica si el usuario tiene un rol específico.
     */
    public static function hasRole(string $role): bool
    {
        return self::getRole() === $role;
    }

    /**
     * Verifica si el usuario tiene uno de varios roles.
     */
    public static function hasAnyRole(array $roles): bool
    {
        return in_array(self::getRole(), $roles, true);
    }

    /**
     * Realiza login de un usuario.
     */
    public static function login(array $user): void
    {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_nombre'] = $user['nombre'];
        $_SESSION['user_rol'] = $user['rol'];
        $_SESSION['login_time'] = time();
    }

    /**
     * Realiza logout del usuario.
     */
    public static function logout(): void
    {
        session_destroy();
    }

    /**
     * Obtiene el nombre del rol en formato legible.
     */
    public static function getRoleName(string $rol = null): string
    {
        $rol = $rol ?? self::getRole();

        $roles = [
            'coordinador' => 'Coordinador',
            'administrador' => 'Administrador',
            'enfermero' => 'Enfermero',
        ];

        return $roles[$rol] ?? 'Usuario';
    }
}
