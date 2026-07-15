<?php

class Page extends Model
{
    protected static string $table = 'pages';

    public static function findBySlug(string $slug): ?array
    {
        return self::findBy('slug', $slug);
    }

    public static function published(): array
    {
        return self::where("status = 'published'", [], 'title ASC');
    }
}
