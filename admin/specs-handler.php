<?php

$pageTitle = 'Spec Definitions';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
        $message = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'create' || $action === 'update') {
            $id = (int)($_POST['id'] ?? 0);
            $key = Security::generateSlug($_POST['key'] ?? '');
            $label = Security::sanitize($_POST['label'] ?? '');
            $dataType = $_POST['data_type'] ?? 'text';
            $unit = Security::sanitize($_POST['unit'] ?? '');
            $options = Security::sanitize($_POST['options'] ?? '');

            if ($key && $label) {
                $data = [
                    'key' => $key,
                    'label' => $label,
                    'data_type' => $dataType,
                    'unit' => $unit,
                    'options' => $options,
                ];
                if ($action === 'create') {
                    if (!Database::exists('spec_definitions', 'key', $key)) {
                        SpecDefinition::create($data);
                        $message = 'Spec definition created.';
                    } else {
                        $message = 'Key already exists.';
                    }
                } else {
                    SpecDefinition::update($id, $data);
                    $message = 'Spec definition updated.';
                }
            } else {
                $message = 'Key and label are required.';
            }
        }

        if ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id) {
                Database::delete('product_specs', 'spec_definition_id = ?', [$id]);
                SpecDefinition::delete($id);
                $message = 'Spec definition deleted.';
            }
        }
    }
}

$defs = SpecDefinition::all();
$editDef = null;
if (isset($_GET['edit'])) {
    foreach ($defs as $d) {
        if ($d['id'] == $_GET['edit']) { $editDef = $d; break; }
    }
}

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
            <span class="text-deep-royal font-medium">Spec Definitions</span>
        </nav>
    </div>
    <button class="bg-vibrant-amber text-charcoal-text px-6 py-3 rounded-lg font-label-caps flex items-center gap-2 hover:brightness-105 transition-all shadow-sm" onclick="document.getElementById('specForm').classList.toggle('hidden')">
        <span class="material-symbols-outlined">add</span> New Spec
    </button>
</div>

<div id="specForm" class="glass-card rounded-xl border border-on-surface/5 p-6 mb-6 <?= $editDef ? '' : 'hidden' ?>">
    <form method="POST" class="grid grid-cols-1 md:grid-cols-6 gap-4">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
        <input type="hidden" name="action" value="<?= $editDef ? 'update' : 'create' ?>">
        <input type="hidden" name="id" value="<?= $editDef['id'] ?? 0 ?>">
        <div>
            <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Key</label>
            <input class="w-full border border-divider-gray rounded-lg p-2 text-sm focus:ring-2 focus:ring-deep-royal focus:outline-none" name="key" value="<?= Security::h($editDef['key'] ?? '') ?>" required>
        </div>
        <div>
            <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Label</label>
            <input class="w-full border border-divider-gray rounded-lg p-2 text-sm focus:ring-2 focus:ring-deep-royal focus:outline-none" name="label" value="<?= Security::h($editDef['label'] ?? '') ?>" required>
        </div>
        <div>
            <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Type</label>
            <select class="w-full border border-divider-gray rounded-lg p-2 text-sm focus:ring-2 focus:ring-deep-royal focus:outline-none" name="data_type">
                <?php foreach (['text', 'number', 'enum'] as $t): ?>
                    <option value="<?= $t ?>" <?= selected($editDef['data_type'] ?? 'text', $t) ?>><?= ucfirst($t) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Unit</label>
            <input class="w-full border border-divider-gray rounded-lg p-2 text-sm focus:ring-2 focus:ring-deep-royal focus:outline-none" name="unit" value="<?= Security::h($editDef['unit'] ?? '') ?>" placeholder="e.g. Tons, V, kW">
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Options (comma-separated, for enum)</label>
            <input class="w-full border border-divider-gray rounded-lg p-2 text-sm focus:ring-2 focus:ring-deep-royal focus:outline-none" name="options" value="<?= Security::h($editDef['options'] ?? '') ?>" placeholder="Option A, Option B, Option C">
        </div>
        <div class="md:col-span-6 flex gap-3">
            <button class="bg-deep-royal text-pure-white px-6 py-2 rounded-lg font-label-caps hover:brightness-110 transition-all"><?= $editDef ? 'Update' : 'Create' ?></button>
            <?php if ($editDef): ?>
                <a href="?" class="px-4 py-2 rounded-lg border border-divider-gray text-sm hover:bg-surface-container transition-all">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="glass-card rounded-xl overflow-hidden border border-on-surface/5">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-low border-b border-on-surface/10">
                <th class="px-6 py-4 font-label-caps text-on-surface-variant">Key</th>
                <th class="px-6 py-4 font-label-caps text-on-surface-variant">Label</th>
                <th class="px-6 py-4 font-label-caps text-on-surface-variant">Type</th>
                <th class="px-6 py-4 font-label-caps text-on-surface-variant">Unit</th>
                <th class="px-6 py-4 font-label-caps text-on-surface-variant text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-on-surface/5">
            <?php if (empty($defs)): ?>
                <tr><td colspan="5" class="px-6 py-12 text-center text-on-surface-variant">No spec definitions yet.</td></tr>
            <?php endif; ?>
            <?php foreach ($defs as $def): ?>
            <tr class="hover:bg-surface-bright transition-colors">
                <td class="px-6 py-4 font-mono text-sm"><?= Security::h($def['key']) ?></td>
                <td class="px-6 py-4 font-medium"><?= Security::h($def['label']) ?></td>
                <td class="px-6 py-4 text-sm text-on-surface-variant"><?= $def['data_type'] ?></td>
                <td class="px-6 py-4 text-sm text-on-surface-variant"><?= Security::h($def['unit'] ?: '—') ?></td>
                <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                        <a href="?edit=<?= $def['id'] ?>" class="p-2 hover:bg-surface-container rounded-lg text-on-surface-variant transition-colors"><span class="material-symbols-outlined">edit</span></a>
                        <form method="POST" class="inline" onsubmit="return confirm('Delete this spec definition? Existing values will be removed from products.');">
                            <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $def['id'] ?>">
                            <button class="p-2 hover:bg-error/10 rounded-lg transition-colors" style="color: #ba1a1a;"><span class="material-symbols-outlined">delete</span></button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once BASE_PATH . 'includes/admin-footer.php'; ?>
