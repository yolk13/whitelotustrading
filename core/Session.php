<?php

class Session
{
    private static bool $initialized = false;

    public static function init(): void
    {
        if (self::$initialized) {
            return;
        }

        session_set_save_handler(
            [__CLASS__, 'open'],
            [__CLASS__, 'close'],
            [__CLASS__, 'read'],
            [__CLASS__, 'write'],
            [__CLASS__, 'destroy'],
            [__CLASS__, 'gc']
        );

        session_name('WL_SESSION');
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Strict',
        ]);

        session_start();
        self::$initialized = true;
    }

    public static function open(string $savePath, string $sessionName): bool
    {
        return true;
    }

    public static function close(): bool
    {
        return true;
    }

    public static function read(string $sessionId): string
    {
        $row = Database::fetch(
            "SELECT payload FROM sessions WHERE id = ? AND last_activity >= ?",
            [$sessionId, time() - SESSION_LIFETIME]
        );
        return $row ? $row['payload'] : '';
    }

    public static function write(string $sessionId, string $data): bool
    {
        $exists = Database::exists('sessions', 'id', $sessionId);
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $time = time();

        if ($exists) {
            Database::query(
                "UPDATE sessions SET payload = ?, last_activity = ?, ip_address = ?, user_agent = ? WHERE id = ?",
                [$data, $time, $ip, $ua, $sessionId]
            );
        } else {
            Database::query(
                "INSERT INTO sessions (id, payload, last_activity, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)",
                [$sessionId, $data, $time, $ip, $ua]
            );
        }
        return true;
    }

    public static function destroy(string $sessionId): bool
    {
        Database::delete('sessions', 'id = ?', [$sessionId]);
        return true;
    }

    public static function gc(int $maxLifetime): int
    {
        Database::query("DELETE FROM sessions WHERE last_activity < ?", [time() - $maxLifetime]);
        return Database::query("SELECT changes()")->fetchColumn();
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroySession(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }
}
