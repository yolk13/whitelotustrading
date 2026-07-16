<?php

class SpecDefinition extends Model
{
    public static string $table = 'spec_definitions';

    public static function all(string $orderBy = 'sort_order, label'): array
    {
        $orderBy = self::sanitizeOrderBy($orderBy);
        return Database::fetchAll("SELECT * FROM " . static::$table . " ORDER BY {$orderBy}");
    }

    public static function withValues(int $productId): array
    {
        $defs = self::all();
        $specs = Product::getSpecs($productId);
        $specMap = [];
        foreach ($specs as $s) {
            $specMap[$s['spec_definition_id']] = $s['value'];
        }
        foreach ($defs as &$def) {
            $def['value'] = $specMap[$def['id']] ?? '';
        }
        return $defs;
    }
}
