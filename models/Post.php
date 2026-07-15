<?php

class Post extends Model
{
    protected static string $table = 'posts';

    public static function findBySlug(string $slug): ?array
    {
        return self::findBy('slug', $slug);
    }

    public static function published(): array
    {
        return self::where("status = 'published'", [], 'published_at DESC');
    }

    public static function recentPosts(int $limit = 3): array
    {
        return Database::fetchAll(
            "SELECT * FROM posts WHERE status = 'published' ORDER BY published_at DESC LIMIT ?",
            [$limit]
        );
    }

    public static function byCategory(string $category): array
    {
        return self::where("category = ? AND status = 'published'", [$category], 'published_at DESC');
    }

    public static function categories(): array
    {
        return Database::fetchAll(
            "SELECT DISTINCT category FROM posts WHERE status = 'published' AND category IS NOT NULL ORDER BY category"
        );
    }

    public static function paginatePublished(int $page = 1, int $perPage = 6, string $category = ''): array
    {
        $where = "status = 'published'";
        $params = [];

        if ($category) {
            $where .= " AND category = ?";
            $params[] = $category;
        }

        return self::paginate($page, $perPage, $where, $params, 'published_at DESC');
    }

    public static function paginateAdmin(int $page = 1, int $perPage = 10, string $status = '', string $category = ''): array
    {
        $where = '1=1';
        $params = [];

        if ($status) {
            $where .= " AND status = ?";
            $params[] = $status;
        }

        if ($category) {
            $where .= " AND category = ?";
            $params[] = $category;
        }

        return self::paginate($page, $perPage, $where, $params, 'created_at DESC');
    }
}
