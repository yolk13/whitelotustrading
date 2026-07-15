<?php

class Product extends Model
{
    protected static string $table = 'products';

    public static function findBySlug(string $slug): ?array
    {
        return self::findBy('slug', $slug);
    }

    public static function active(): array
    {
        return self::where("status = 'active'", [], 'created_at DESC');
    }

    public static function byDivision(string $division): array
    {
        return self::where("division = ? AND status = 'active'", [$division], 'created_at DESC');
    }

    public static function search(string $query): array
    {
        return Database::fetchAll(
            "SELECT * FROM products WHERE status = 'active' AND (name LIKE ? OR sku LIKE ? OR description LIKE ?) ORDER BY name ASC",
            ["%{$query}%", "%{$query}%", "%{$query}%"]
        );
    }

    public static function paginateActive(int $page = 1, int $perPage = 10, string $division = '', string $search = ''): array
    {
        $where = "status = 'active'";
        $params = [];

        if ($division) {
            $where .= " AND division = ?";
            $params[] = $division;
        }

        if ($search) {
            $where .= " AND (name LIKE ? OR sku LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        return self::paginate($page, $perPage, $where, $params, 'created_at DESC');
    }

    public static function paginateAdmin(int $page = 1, int $perPage = 10, string $division = '', string $status = '', string $search = ''): array
    {
        $where = '1=1';
        $params = [];

        if ($division) {
            $where .= " AND division = ?";
            $params[] = $division;
        }

        if ($status) {
            $where .= " AND status = ?";
            $params[] = $status;
        }

        if ($search) {
            $where .= " AND (name LIKE ? OR sku LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        return self::paginate($page, $perPage, $where, $params, 'created_at DESC');
    }
}
