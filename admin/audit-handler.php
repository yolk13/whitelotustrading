<?php

$pageTitle = 'Audit Log';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'clear') {
    if (Security::validateCsrf($_POST['csrf_token'] ?? null)) {
        Database::delete('audit_log', '1=1', []);
        $message = 'Audit log cleared.';
    }
}

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 50;

$total = Database::count('audit_log', '1=1', []);
$totalPages = max(1, ceil($total / $perPage));
$page = max(1, min($page, $totalPages));
$offset = ($page - 1) * $perPage;

$logs = Database::fetchAll(
    "SELECT a.*, u.display_name, u.username
     FROM audit_log a
     LEFT JOIN users u ON a.admin_user_id = u.id
     ORDER BY a.created_at DESC
     LIMIT ? OFFSET ?",
    [$perPage, $offset]
);

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
            <span class="text-deep-royal font-medium">Audit Log</span>
        </nav>
    </div>
    <form method="POST" onsubmit="return confirm('Clear entire audit log?');">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
        <input type="hidden" name="action" value="clear">
        <button class="px-4 py-2 rounded-lg border border-error/30 text-error font-label-caps hover:bg-error/10 transition-all">Clear Log</button>
    </form>
</div>

<div class="glass-card rounded-xl overflow-hidden border border-on-surface/5" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(20px);">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-low border-b border-on-surface/10">
                <th class="px-4 py-3 font-label-caps text-on-surface-variant">Time</th>
                <th class="px-4 py-3 font-label-caps text-on-surface-variant">Admin</th>
                <th class="px-4 py-3 font-label-caps text-on-surface-variant">Action</th>
                <th class="px-4 py-3 font-label-caps text-on-surface-variant">Entity</th>
                <th class="px-4 py-3 font-label-caps text-on-surface-variant">IP</th>
                <th class="px-4 py-3 font-label-caps text-on-surface-variant">Changes</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-on-surface/5">
            <?php if (empty($logs)): ?>
                <tr><td colspan="6" class="px-4 py-12 text-center text-on-surface-variant">No audit log entries.</td></tr>
            <?php endif; ?>
            <?php foreach ($logs as $log): ?>
            <tr class="hover:bg-surface-bright transition-colors text-sm">
                <td class="px-4 py-3 text-on-surface-variant whitespace-nowrap"><?= formatDate($log['created_at'], 'M d, H:i') ?></td>
                <td class="px-4 py-3"><?= Security::h($log['display_name'] ?? $log['username'] ?? 'System') ?></td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded text-xs font-bold
                        <?= $log['action'] === 'create' ? 'bg-green-100 text-green-800' : '' ?>
                        <?= $log['action'] === 'update' ? 'bg-blue-100 text-blue-800' : '' ?>
                        <?= $log['action'] === 'delete' ? 'bg-red-100 text-red-800' : '' ?>
                    "><?= Security::h($log['action']) ?></span>
                </td>
                <td class="px-4 py-3"><?= Security::h($log['entity_type']) ?> #<?= (int)$log['entity_id'] ?></td>
                <td class="px-4 py-3 text-on-surface-variant font-mono text-xs"><?= Security::h($log['ip_address']) ?></td>
                <td class="px-4 py-3 max-w-xs">
                    <?php if ($log['changes']): ?>
                        <button onclick="toggleChanges(<?= $log['id'] ?>)" class="text-deep-royal underline text-xs cursor-pointer">View</button>
                        <pre id="changes-<?= $log['id'] ?>" class="hidden text-xs text-on-surface-variant bg-surface-container p-2 rounded mt-1 overflow-x-auto max-h-32"><?= Security::h($log['changes']) ?></pre>
                    <?php else: ?>
                        <span class="text-on-surface-variant">—</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if ($totalPages > 1): ?>
    <div class="px-4 py-3 bg-surface-container-lowest flex justify-between items-center text-sm border-t border-on-surface/5">
        <p class="text-on-surface-variant">Page <?= $page ?> of <?= $totalPages ?> (<?= $total ?> entries)</p>
        <div class="flex gap-2">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>" class="w-8 h-8 flex items-center justify-center rounded border border-divider-gray hover:bg-surface-container transition-colors"><span class="material-symbols-outlined">chevron_left</span></a>
            <?php endif; ?>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>" class="w-8 h-8 flex items-center justify-center rounded border border-divider-gray hover:bg-surface-container transition-colors"><span class="material-symbols-outlined">chevron_right</span></a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function toggleChanges(id) {
    var el = document.getElementById('changes-' + id);
    if (el) el.classList.toggle('hidden');
}
</script>

<?php require_once BASE_PATH . 'includes/admin-footer.php'; ?>
