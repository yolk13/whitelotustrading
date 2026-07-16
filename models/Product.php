<?php

class Product extends Model
{
    public static string $table = 'products';
    protected static bool $softDelete = true;

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
            "SELECT * FROM products WHERE status = 'active' AND deleted_at IS NULL AND (name LIKE ? OR sku LIKE ? OR description LIKE ?) ORDER BY name ASC",
            ["%{$query}%", "%{$query}%", "%{$query}%"]
        );
    }

    public static function paginateActive(int $page = 1, int $perPage = 10, string $division = '', string $search = '', array $ids = []): array
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

        if (!empty($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $where .= " AND id IN ({$placeholders})";
            $params = array_merge($params, $ids);
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

    public static function getSpecs(int $productId): array
    {
        return Database::fetchAll(
            "SELECT ps.*, sd.key, sd.label, sd.data_type, sd.unit, sd.options
             FROM product_specs ps
             JOIN spec_definitions sd ON ps.spec_definition_id = sd.id
             WHERE ps.product_id = ?
             ORDER BY sd.sort_order, sd.label",
            [$productId]
        );
    }

    public static function saveSpec(int $productId, int $specDefinitionId, string $value): void
    {
        Database::upsert('product_specs', [
            'product_id' => $productId,
            'spec_definition_id' => $specDefinitionId,
            'value' => $value,
        ], ['product_id', 'spec_definition_id']);
    }

    public static function deleteSpec(int $productId, int $specDefinitionId): void
    {
        Database::delete('product_specs', 'product_id = ? AND spec_definition_id = ?', [$productId, $specDefinitionId]);
    }

    public static function getVariants(int $productId): array
    {
        return Database::fetchAll(
            "SELECT * FROM product_variants WHERE product_id = ? ORDER BY sort_order, id",
            [$productId]
        );
    }

    public static function saveVariant(int $productId, array $data): int
    {
        $data['product_id'] = $productId;
        if (!empty($data['id'])) {
            $id = (int)$data['id'];
            Database::update('product_variants', [
                'variant_label' => $data['variant_label'],
                'sku_suffix' => $data['sku_suffix'] ?? '',
                'price' => $data['price'] !== '' ? (float)$data['price'] : null,
                'stock' => (int)($data['stock'] ?? 0),
                'sort_order' => (int)($data['sort_order'] ?? 0),
            ], 'id = ?', [$id]);
            return $id;
        }
        return Database::insert('product_variants', [
            'product_id' => $productId,
            'variant_label' => $data['variant_label'],
            'sku_suffix' => $data['sku_suffix'] ?? '',
            'price' => $data['price'] !== '' ? (float)$data['price'] : null,
            'stock' => (int)($data['stock'] ?? 0),
            'sort_order' => (int)($data['sort_order'] ?? 0),
        ]);
    }

    public static function deleteVariant(int $variantId): void
    {
        Database::delete('product_variants', 'id = ?', [$variantId]);
    }
}
