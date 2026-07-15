<?php

$pageTitle = 'Category Management';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
        $message = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'create') {
            $name = Security::sanitize($_POST['name'] ?? '');
            $slug = Security::generateSlug($name);
            $desc = Security::sanitize($_POST['description'] ?? '');
            if ($name && !Database::exists('categories', 'slug', $slug)) {
                Database::insert('categories', ['name' => $name, 'slug' => $slug, 'description' => $desc]);
                $message = 'Category created.';
            } else {
                $message = 'Category already exists or name is empty.';
            }
        }

        if ($action === 'update') {
            $id = (int)($_POST['id'] ?? 0);
            $name = Security::sanitize($_POST['name'] ?? '');
            $desc = Security::sanitize($_POST['description'] ?? '');
            if ($id && $name) {
                Database::update('categories', ['name' => $name, 'description' => $desc], 'id = ?', [$id]);
                $message = 'Category updated.';
            }
        }

        if ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id) {
                Database::delete('categories', 'id = ?', [$id]);
                $message = 'Category deleted.';
            }
        }
    }
}

$categories = Database::fetchAll("SELECT * FROM categories ORDER BY name ASC");

require_once BASE_PATH . 'includes/admin-header.php';
?>

<?php if ($message): ?>
    <div class="bg-primary-fixed text-deep-royal px-6 py-3 rounded-lg font-body-md" style="background-color: #dbe1ff;"><?= Security::h($message) ?></div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
    <div class="lg:col-span-7">
        <div class="glass-card rounded-xl overflow-hidden border border-on-surface/5">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low border-b border-on-surface/10">
                        <th class="px-6 py-4 font-label-caps text-on-surface-variant">Name</th>
                        <th class="px-6 py-4 font-label-caps text-on-surface-variant">Slug</th>
                        <th class="px-6 py-4 font-label-caps text-on-surface-variant">Description</th>
                        <th class="px-6 py-4 font-label-caps text-on-surface-variant text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-on-surface/5">
                    <?php if (empty($categories)): ?>
                        <tr><td colspan="4" class="px-6 py-12 text-center text-on-surface-variant">No categories.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($categories as $cat): ?>
                    <tr class="hover:bg-surface-bright transition-colors">
                        <td class="px-6 py-4 font-medium text-deep-royal"><?= Security::h($cat['name']) ?></td>
                        <td class="px-6 py-4 text-sm text-on-surface-variant"><?= Security::h($cat['slug']) ?></td>
                        <td class="px-6 py-4 text-sm text-on-surface-variant max-w-[200px] truncate"><?= Security::h($cat['description'] ?? '') ?></td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-1">
                                <button class="p-2 hover:bg-surface-container rounded-lg text-on-surface-variant transition-colors" onclick="openEditCat(<?= $cat['id'] ?>, '<?= Security::h(addslashes($cat['name'])) ?>', '<?= Security::h(addslashes($cat['description'] ?? '')) ?>')">
                                    <span class="material-symbols-outlined">edit</span>
                                </button>
                                <form method="POST" class="inline" onsubmit="return confirm('Delete this category?');">
                                    <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                    <button class="p-2 hover:bg-error/10 rounded-lg transition-colors" style="color: #ba1a1a;"><span class="material-symbols-outlined">delete</span></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="lg:col-span-5">
        <div class="glass-card rounded-xl border border-on-surface/5 p-8" id="catForm">
            <h3 class="font-headline-sm text-headline-sm text-deep-royal mb-6" id="catFormTitle">Add Category</h3>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
                <input type="hidden" name="action" id="catFormAction" value="create">
                <input type="hidden" name="id" id="catFormId" value="">
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Name</label>
                    <input class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="name" id="catInputName" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Description</label>
                    <textarea class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="description" id="catInputDesc" rows="3"></textarea>
                </div>
                <div class="flex gap-4">
                    <button type="submit" class="bg-deep-royal text-pure-white px-6 py-3 rounded-lg font-label-caps hover:brightness-110 transition-all shadow-md">Save</button>
                    <button type="button" class="px-4 py-3 rounded-lg font-label-caps border border-divider-gray hover:bg-surface-container transition-all hidden" id="catCancelBtn" onclick="resetCatForm()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditCat(id, name, desc) {
    document.getElementById('catFormAction').value = 'update';
    document.getElementById('catFormId').value = id;
    document.getElementById('catFormTitle').textContent = 'Edit Category';
    document.getElementById('catInputName').value = name;
    document.getElementById('catInputDesc').value = desc;
    document.getElementById('catCancelBtn').classList.remove('hidden');
}

function resetCatForm() {
    document.getElementById('catFormAction').value = 'create';
    document.getElementById('catFormId').value = '';
    document.getElementById('catFormTitle').textContent = 'Add Category';
    document.getElementById('catInputName').value = '';
    document.getElementById('catInputDesc').value = '';
    document.getElementById('catCancelBtn').classList.add('hidden');
}
</script>

<?php require_once BASE_PATH . 'includes/admin-footer.php'; ?>
