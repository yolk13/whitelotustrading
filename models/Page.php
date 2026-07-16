<?php

class Page extends Model
{
    public static string $table = 'pages';
    protected static bool $softDelete = true;

    public static function findBySlug(string $slug): ?array
    {
        return self::findBy('slug', $slug);
    }

    public static function published(): array
    {
        return self::where("status = 'published'", [], 'title ASC');
    }
}
