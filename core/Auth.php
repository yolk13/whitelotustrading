<?php

class Auth
{
    public static function attempt(string $username, string $password): bool
    {
        if (!Security::rateLimitCheck($username)) {
            Session::set('login_error', 'Account locked. Try again later.');
            return false;
        }

        $user = Database::fetch(
            "SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1",
            [$username, $username]
        );

        if (!$user) {
            usleep(500000);
            Session::set('login_error', 'Invalid credentials');
            return false;
        }

        if (!Security::verifyPassword($password, $user['password_hash'])) {
            Security::rateLimitHit($username);
            usleep(1000000);
            Session::set('login_error', 'Invalid credentials');
            return false;
        }

        Security::rateLimitReset($username);

        Database::query(
            "UPDATE users SET last_login = datetime('now') WHERE id = ?",
            [$user['id']]
        );

        Session::set('user_id', $user['id']);
        Session::set('username', $user['username']);
        Session::set('display_name', $user['display_name']);
        Session::set('role', $user['role']);
        Session::set('logged_in', true);
        Session::set('login_time', time());

        return true;
    }

    public static function check(): bool
    {
        if (!Session::get('logged_in')) {
            return false;
        }
        $userId = Session::get('user_id');
        if (!$userId) {
            return false;
        }
        return true;
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        return Database::fetch("SELECT id, username, email, display_name, role, last_login FROM users WHERE id = ?", [Session::get('user_id')]);
    }

    public static function id(): ?int
    {
        return Session::get('user_id');
    }

    public static function require(): void
    {
        if (!self::check()) {
            Session::set('redirect_after_login', $_SERVER['REQUEST_URI'] ?? '/admin');
            header('Location: /admin/login');
            exit;
        }
    }

    public static function logout(): void
    {
        Session::destroySession();
    }
}
