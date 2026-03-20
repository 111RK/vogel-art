<?php

class Auth
{
    public static function login(string $email, string $password): bool
    {
        $user = Database::fetch(
            'SELECT * FROM admin_users WHERE email = ?',
            [$email]
        );

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            return true;
        }
        return false;
    }

    public static function check(): bool
    {
        return isset($_SESSION['admin_id']);
    }

    public static function requireAuth(): void
    {
        if (!self::check()) {
            header('Location: /admin/login');
            exit;
        }
    }

    public static function logout(): void
    {
        session_destroy();
        header('Location: /admin/login');
        exit;
    }

    public static function currentUser(): ?array
    {
        if (!self::check()) return null;
        return [
            'id' => $_SESSION['admin_id'],
            'name' => $_SESSION['admin_name'],
        ];
    }
}
