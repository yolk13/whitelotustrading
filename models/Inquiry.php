<?php

class Inquiry extends Model
{
    protected static string $table = 'inquiries';

    public static function recent(int $limit = 5): array
    {
        return Database::fetchAll(
            "SELECT * FROM inquiries ORDER BY created_at DESC LIMIT ?",
            [$limit]
        );
    }

    public static function unread(): array
    {
        return self::where("is_read = 0", [], 'created_at DESC');
    }

    public static function markRead(int $id): void
    {
        Database::query(
            "UPDATE inquiries SET is_read = 1 WHERE id = ?",
            [$id]
        );
    }

    public static function markStatus(int $id, string $status): void
    {
        Database::query(
            "UPDATE inquiries SET status = ? WHERE id = ?",
            [$status, $id]
        );
    }

    public static function countUnread(): int
    {
        return self::count("is_read = 0");
    }

    public static function paginateFiltered(int $page = 1, int $perPage = 10, string $filter = 'all', string $search = ''): array
    {
        $where = '1=1';
        $params = [];

        if ($filter === 'unread') {
            $where .= " AND is_read = 0";
        } elseif ($filter === 'flagged') {
            $where .= " AND status = 'flagged'";
        }

        if ($search) {
            $where .= " AND (name LIKE ? OR email LIKE ? OR subject LIKE ? OR company LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        return self::paginate($page, $perPage, $where, $params, 'created_at DESC');
    }
}
