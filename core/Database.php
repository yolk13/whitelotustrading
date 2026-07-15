<?php

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dbPath = DB_PATH;
            $dir = dirname($dbPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            self::$instance = new PDO("sqlite:$dbPath", null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            self::$instance->exec('PRAGMA journal_mode=WAL');
            self::$instance->exec('PRAGMA foreign_keys=ON');
            self::initializeSchema();
        }
        return self::$instance;
    }

    private static function initializeSchema(): void
    {
        self::$instance->exec('
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                email TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL,
                display_name TEXT,
                role TEXT DEFAULT "admin",
                last_login DATETIME,
                login_attempts INTEGER DEFAULT 0,
                locked_until DATETIME,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');
        self::$instance->exec('
            CREATE TABLE IF NOT EXISTS sessions (
                id TEXT PRIMARY KEY,
                user_id INTEGER,
                ip_address TEXT,
                user_agent TEXT,
                last_activity DATETIME,
                payload TEXT,
                FOREIGN KEY (user_id) REFERENCES users(id)
            )
        ');
        self::$instance->exec('
            CREATE TABLE IF NOT EXISTS pages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                slug TEXT NOT NULL UNIQUE,
                title TEXT NOT NULL,
                content TEXT,
                meta_description TEXT,
                status TEXT DEFAULT "draft",
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');
        self::$instance->exec('
            CREATE TABLE IF NOT EXISTS products (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                slug TEXT NOT NULL UNIQUE,
                sku TEXT,
                division TEXT,
                category TEXT,
                description TEXT,
                specs TEXT,
                price DECIMAL(10,2),
                stock INTEGER DEFAULT 0,
                unit TEXT DEFAULT "Units",
                status TEXT DEFAULT "draft",
                image_url TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');
        self::$instance->exec('
            CREATE TABLE IF NOT EXISTS inquiries (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                phone TEXT,
                company TEXT,
                division TEXT,
                subject TEXT NOT NULL,
                message TEXT NOT NULL,
                status TEXT DEFAULT "new",
                is_read INTEGER DEFAULT 0,
                admin_notes TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');
        self::$instance->exec('
            CREATE TABLE IF NOT EXISTS posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                slug TEXT NOT NULL UNIQUE,
                content TEXT,
                excerpt TEXT,
                featured_image TEXT,
                category TEXT,
                status TEXT DEFAULT "draft",
                published_at DATETIME,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');

        $existingAdmin = self::fetch("SELECT id FROM users WHERE username = 'admin'");
        if (!$existingAdmin) {
            $hash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]);
            self::insert('users', [
                'username' => 'admin',
                'email' => 'admin@whitelotustrading.com',
                'password_hash' => $hash,
                'display_name' => 'Admin User',
                'role' => 'super_admin',
            ]);
        }
    }

    public static function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetch(string $sql, array $params = []): ?array
    {
        $result = self::query($sql, $params)->fetch();
        return $result ?: null;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    public static function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        self::query("INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})", array_values($data));
        return (int)self::getInstance()->lastInsertId();
    }

    public static function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $sets = implode(', ', array_map(fn($col) => "{$col} = ?", array_keys($data)));
        $params = array_merge(array_values($data), $whereParams);
        self::query("UPDATE {$table} SET {$sets} WHERE {$where}", $params);
        return self::query("SELECT changes()")->fetchColumn();
    }

    public static function delete(string $table, string $where, array $params = []): int
    {
        self::query("DELETE FROM {$table} WHERE {$where}", $params);
        return self::query("SELECT changes()")->fetchColumn();
    }

    public static function exists(string $table, string $column, mixed $value): bool
    {
        return (bool)self::fetch(
            "SELECT 1 FROM {$table} WHERE {$column} = ? LIMIT 1",
            [$value]
        );
    }

    public static function count(string $table, string $where = '1=1', array $params = []): int
    {
        return (int)self::fetch("SELECT COUNT(*) as cnt FROM {$table} WHERE {$where}", $params)['cnt'];
    }
}
