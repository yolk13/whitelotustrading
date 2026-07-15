<?php

class Model
{
    protected static string $table;
    protected static string $primaryKey = 'id';

    public static function all(string $orderBy = 'id DESC'): array
    {
        $orderBy = self::sanitizeOrderBy($orderBy);
        return Database::fetchAll("SELECT * FROM " . static::$table . " ORDER BY {$orderBy}");
    }

    public static function find(int $id): ?array
    {
        return Database::fetch(
            "SELECT * FROM " . static::$table . " WHERE " . static::$primaryKey . " = ?",
            [$id]
        );
    }

    public static function findBy(string $column, mixed $value): ?array
    {
        return Database::fetch(
            "SELECT * FROM " . static::$table . " WHERE {$column} = ? LIMIT 1",
            [$value]
        );
    }

    public static function where(string $where, array $params = [], string $orderBy = 'id DESC'): array
    {
        $orderBy = self::sanitizeOrderBy($orderBy);
        return Database::fetchAll(
            "SELECT * FROM " . static::$table . " WHERE {$where} ORDER BY {$orderBy}",
            $params
        );
    }

    private static function sanitizeOrderBy(string $orderBy): string
    {
        if (!preg_match('/^[a-zA-Z0-9_\s,]+(?:ASC|DESC)?$/i', $orderBy)) {
            return 'id DESC';
        }
        return $orderBy;
    }

    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return Database::insert(static::$table, $data);
    }

    public static function update(int $id, array $data): int
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return Database::update(
            static::$table,
            $data,
            static::$primaryKey . ' = ?',
            [$id]
        );
    }

    public static function delete(int $id): int
    {
        return Database::delete(
            static::$table,
            static::$primaryKey . ' = ?',
            [$id]
        );
    }

    public static function count(string $where = '1=1', array $params = []): int
    {
        return Database::count(static::$table, $where, $params);
    }

    public static function paginate(int $page = 1, int $perPage = 10, string $where = '1=1', array $params = [], string $orderBy = 'id DESC'): array
    {
        $orderBy = self::sanitizeOrderBy($orderBy);
        $total = self::count($where, $params);
        $totalPages = max(1, ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;

        $items = Database::fetchAll(
            "SELECT * FROM " . static::$table . " WHERE {$where} ORDER BY {$orderBy} LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );

        return [
            'items' => $items,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'totalPages' => $totalPages,
            'hasPrev' => $page > 1,
            'hasNext' => $page < $totalPages,
            'prevPage' => $page - 1,
            'nextPage' => $page + 1,
        ];
    }

    public static function slugExists(string $slug, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            return (bool)Database::fetch(
                "SELECT 1 FROM " . static::$table . " WHERE slug = ? AND " . static::$primaryKey . " != ? LIMIT 1",
                [$slug, $excludeId]
            );
        }
        return Database::exists(static::$table, 'slug', $slug);
    }
}
