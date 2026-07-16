<?php

Auth::requireRole('admin');
$pageTitle = 'Subscribers Management';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
        $message = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';
        if ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id) {
                Database::delete('subscribers', 'id = ?', [$id]);
                $message = 'Subscriber deleted.';
            }
        }
    }
}

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;
$total = Database::count('subscribers');
$subscribers = Database::fetchAll("SELECT * FROM subscribers ORDER BY created_at DESC LIMIT ? OFFSET ?", [$perPage, $offset]);
$totalPages = max(1, (int)ceil($total / $perPage));

require_once BASE_PATH . 'includes/admin-header.php';
?>

<?php if ($message): ?>
    <div class="bg-primary-fixed text-deep-royal px-6 py-3 rounded-lg font-body-md" style="background-color: #dbe1ff;"><?= Security::h($message) ?></div>
<?php endif; ?>

<div class="flex justify-between items-end mb-6">
    <div>
        <nav class="flex gap-2 items-center text-on-surface-variant text-sm">
            <span>Admin</span>
            <span class="material-symbols-outlined text-xs">chevron_right</span>
            <span class="text-deep-royal font-medium">Subscribers</span>
        </nav>
    </div>
    <p class="text-sm text-on-surface-variant"><?= $total ?> subscriber<?= $total !== 1 ? 's' : '' ?></p>
</div>

<div class="glass-card rounded-xl overflow-hidden border border-on-surface/5">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-low border-b border-on-surface/10">
                <th class="px-6 py-4 font-label-caps text-on-surface-variant">Email</th>
                <th class="px-6 py-4 font-label-caps text-on-surface-variant">Name</th>
                <th class="px-6 py-4 font-label-caps text-on-surface-variant">Status</th>
                <th class="px-6 py-4 font-label-caps text-on-surface-variant">Date</th>
                <th class="px-6 py-4 font-label-caps text-on-surface-variant text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-on-surface/5">
            <?php if (empty($subscribers)): ?>
                <tr><td colspan="5" class="px-6 py-12 text-center text-on-surface-variant">No subscribers yet.</td></tr>
            <?php endif; ?>
            <?php foreach ($subscribers as $s): ?>
            <tr class="hover:bg-surface-bright transition-colors">
                <td class="px-6 py-4 font-medium text-deep-royal"><?= Security::h($s['email']) ?></td>
                <td class="px-6 py-4 text-on-surface-variant"><?= Security::h($s['name'] ?? '—') ?></td>
                <td class="px-6 py-4">
                    <span class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full" style="background: <?= $s['status'] === 'active' ? '#10b981' : '#f59e0b' ?>;"></span>
                        <span class="text-sm font-medium"><?= ucfirst(Security::h($s['status'])) ?></span>
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-on-surface-variant"><?= formatDate($s['created_at'], 'M d, Y') ?></td>
                <td class="px-6 py-4 text-right">
                    <form method="POST" class="inline" onsubmit="return confirm('Delete this subscriber?');">
                        <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $s['id'] ?>">
                        <button class="p-2 hover:bg-error/10 rounded-lg transition-colors" style="color: #ba1a1a;"><span class="material-symbols-outlined">delete</span></button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="px-6 py-4 bg-surface-container-lowest flex justify-between items-center text-sm border-t border-on-surface/5">
        <p class="text-on-surface-variant">Showing <?= count($subscribers) ?> of <?= $total ?></p>
        <?php if ($totalPages > 1): ?>
        <div class="flex gap-2">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>" class="w-8 h-8 flex items-center justify-center rounded border border-divider-gray hover:bg-surface-container transition-colors"><span class="material-symbols-outlined">chevron_left</span></a>
            <?php endif; ?>
            <span class="w-8 h-8 flex items-center justify-center rounded bg-deep-royal text-pure-white font-bold text-sm"><?= $page ?></span>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>" class="w-8 h-8 flex items-center justify-center rounded border border-divider-gray hover:bg-surface-container transition-colors"><span class="material-symbols-outlined">chevron_right</span></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once BASE_PATH . 'includes/admin-footer.php'; ?>
