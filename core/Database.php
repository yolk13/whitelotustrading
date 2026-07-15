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
        }
        return self::$instance;
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
