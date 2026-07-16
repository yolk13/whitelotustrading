<?php

$pageTitle = 'Trash';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
        $message = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';
        $type = $_POST['type'] ?? '';
        $id = (int)($_POST['id'] ?? 0);

        if ($action === 'restore' && $id) {
            match ($type) {
                'product' => Product::restore($id),
                'post' => Post::restore($id),
                'page' => Page::restore($id),
                default => null,
            };
            Audit::log('restore', $type, $id, null);
            $message = ucfirst($type) . ' restored.';
        }

        if ($action === 'delete_forever' && $id) {
            $model = match ($type) {
                'product' => Product::class,
                'post' => Post::class,
                'page' => Page::class,
                default => null,
            };
            if ($model) {
                $trashed = $model::findTrashed($id);
                Database::delete($model::$table, 'id = ?', [$id]);
                Audit::log('delete_forever', $type, $id, $trashed ? json_encode(['name' => $trashed['name'] ?? $trashed['title'] ?? '']) : null);
                $message = ucfirst($type) . ' permanently deleted.';
            }
        }
    }
}

$trashed = [];
$trashed['products'] = Database::fetchAll("SELECT id, name, deleted_at FROM products WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC");
$trashed['posts'] = Database::fetchAll("SELECT id, title AS name, deleted_at FROM posts WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC");
$trashed['pages'] = Database::fetchAll("SELECT id, title AS name, deleted_at FROM pages WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC");
$totalTrashed = count($trashed['products']) + count($trashed['posts']) + count($trashed['pages']);

require_once BASE_PATH . 'includes/admin-header.php';
?>

<?php if ($message): ?>
    <div class="bg-primary-fixed text-deep-royal px-6 py-3 rounded-lg font-body-md mb-6" style="background-color: #dbe1ff;"><?= Security::h($message) ?></div>
<?php endif; ?>

<div class="flex justify-between items-end mb-6">
    <div>
        <nav class="flex gap-2 items-center text-on-surface-variant text-sm">
            <span>Admin</span>
            <span class="material-symbols-outlined text-xs">chevron_right</span>
            <span class="text-deep-royal font-medium">Trash</span>
        </nav>
        <p class="text-sm text-on-surface-variant mt-1"><?= $totalTrashed ?> trashed item<?= $totalTrashed !== 1 ? 's' : '' ?></p>
    </div>
</div>

<?php if ($totalTrashed === 0): ?>
    <div class="glass-card rounded-xl p-12 text-center border border-on-surface/5">
        <span class="material-symbols-outlined text-5xl text-on-surface-variant mb-4">delete_sweep</span>
        <p class="text-on-surface-variant">Trash is empty.</p>
    </div>
<?php endif; ?>

<?php $typeMap = ['products' => 'product', 'posts' => 'post', 'pages' => 'page']; ?>
<?php foreach (['products' => 'Products', 'posts' => 'Posts', 'pages' => 'Pages'] as $key => $label): ?>
    <?php if (empty($trashed[$key])) continue; ?>
    <div class="glass-card rounded-xl overflow-hidden border border-on-surface/5 mb-6">
        <div class="px-6 py-3 bg-surface-container-low border-b border-on-surface/10 flex justify-between items-center">
            <h3 class="font-label-caps text-on-surface-variant"><?= $label ?></h3>
            <span class="text-xs text-on-surface-variant"><?= count($trashed[$key]) ?> item<?= count($trashed[$key]) !== 1 ? 's' : '' ?></span>
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low border-b border-on-surface/10">
                    <th class="px-6 py-3 font-label-caps text-on-surface-variant">Name</th>
                    <th class="px-6 py-3 font-label-caps text-on-surface-variant">Deleted At</th>
                    <th class="px-6 py-3 font-label-caps text-on-surface-variant text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-on-surface/5">
                <?php foreach ($trashed[$key] as $item): ?>
                <tr class="hover:bg-surface-bright transition-colors">
                    <td class="px-6 py-3 text-deep-royal font-medium"><?= Security::h($item['name']) ?></td>
                    <td class="px-6 py-3 text-sm text-on-surface-variant"><?= formatDate($item['deleted_at'], 'M d, Y H:i') ?></td>
                    <td class="px-6 py-3 text-right">
                        <div class="flex justify-end gap-2">
                            <form method="POST" class="inline">
                                <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
                                <input type="hidden" name="action" value="restore">
                                <input type="hidden" name="type" value="<?= $typeMap[$key] ?>">
                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                <button class="px-3 py-1.5 rounded text-xs font-bold bg-green-100 text-green-800 hover:bg-green-200 transition-colors">Restore</button>
                            </form>
                            <form method="POST" class="inline" onsubmit="return confirm('Permanently delete this item?');">
                                <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
                                <input type="hidden" name="action" value="delete_forever">
                                <input type="hidden" name="type" value="<?= $typeMap[$key] ?>">
                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                <button class="px-3 py-1.5 rounded text-xs font-bold bg-red-100 text-red-800 hover:bg-red-200 transition-colors">Delete Forever</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endforeach; ?>

<?php require_once BASE_PATH . 'includes/admin-footer.php'; ?>
