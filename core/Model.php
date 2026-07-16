<?php

class Model
{
    public static string $table;
    protected static string $primaryKey = 'id';
    protected static bool $softDelete = false;
    protected static string $deletedAtColumn = 'deleted_at';

    protected static function applySoftDelete(string $where): string
    {
        if (static::$softDelete) {
            $where .= " AND " . static::$deletedAtColumn . " IS NULL";
        }
        return $where;
    }

    public static function all(string $orderBy = 'id DESC'): array
    {
        $orderBy = self::sanitizeOrderBy($orderBy);
        $where = static::$softDelete ? static::$deletedAtColumn . " IS NULL" : '1=1';
        return Database::fetchAll(
            "SELECT * FROM " . static::$table . " WHERE {$where} ORDER BY {$orderBy}"
        );
    }

    public static function find(int $id): ?array
    {
        $where = static::$primaryKey . " = ?";
        if (static::$softDelete) {
            $where .= " AND " . static::$deletedAtColumn . " IS NULL";
        }
        return Database::fetch(
            "SELECT * FROM " . static::$table . " WHERE {$where} LIMIT 1",
            [$id]
        );
    }

    public static function findTrashed(int $id): ?array
    {
        return Database::fetch(
            "SELECT * FROM " . static::$table . " WHERE " . static::$primaryKey . " = ? AND " . static::$deletedAtColumn . " IS NOT NULL LIMIT 1",
            [$id]
        );
    }

    public static function findBy(string $column, mixed $value): ?array
    {
        $where = "{$column} = ?";
        if (static::$softDelete) {
            $where .= " AND " . static::$deletedAtColumn . " IS NULL";
        }
        return Database::fetch(
            "SELECT * FROM " . static::$table . " WHERE {$where} LIMIT 1",
            [$value]
        );
    }

    public static function where(string $where, array $params = [], string $orderBy = 'id DESC'): array
    {
        $orderBy = self::sanitizeOrderBy($orderBy);
        $where = static::applySoftDelete($where);
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
        if (static::$softDelete) {
            return Database::update(
                static::$table,
                [static::$deletedAtColumn => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                static::$primaryKey . ' = ?',
                [$id]
            );
        }
        return Database::delete(
            static::$table,
            static::$primaryKey . ' = ?',
            [$id]
        );
    }

    public static function restore(int $id): int
    {
        if (static::$softDelete) {
            return Database::update(
                static::$table,
                [static::$deletedAtColumn => null, 'updated_at' => date('Y-m-d H:i:s')],
                static::$primaryKey . ' = ?',
                [$id]
            );
        }
        return 0;
    }

    public static function count(string $where = '1=1', array $params = []): int
    {
        $where = static::applySoftDelete($where);
        return Database::count(static::$table, $where, $params);
    }

    public static function paginate(int $page = 1, int $perPage = 10, string $where = '1=1', array $params = [], string $orderBy = 'id DESC'): array
    {
        $orderBy = self::sanitizeOrderBy($orderBy);
        $where = static::applySoftDelete($where);
        $total = Database::count(static::$table, $where, $params);
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
        $where = "slug = ?";
        if (static::$softDelete) {
            $where .= " AND " . static::$deletedAtColumn . " IS NULL";
        }
        if ($excludeId) {
            $where .= " AND " . static::$primaryKey . " != ?";
            return (bool)Database::fetch(
                "SELECT 1 FROM " . static::$table . " WHERE {$where} LIMIT 1",
                [$slug, $excludeId]
            );
        }
        return (bool)Database::fetch(
            "SELECT 1 FROM " . static::$table . " WHERE {$where} LIMIT 1",
            [$slug]
        );
    }
}
