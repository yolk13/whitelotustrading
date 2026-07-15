<?php

class Category extends Model
{
    protected static string $table = 'categories';

    public static function findBySlug(string $slug): ?array
    {
        return self::findBy('slug', $slug);
    }

    public static function asOptions(): array
    {
        return self::all('name ASC');
    }
}
