<?php

$pageTitle = 'Product Management';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
        $message = 'Invalid security token. Please try again.';
        $GLOBALS['errors']['csrf'] = $message;
    } else {
    $action = $_POST['action'] ?? '';

    if ($action === 'create' || $action === 'update') {
        $validator = new Validator();
        $rules = [
            'name' => ['required', 'min:2', 'max:200'],
            'sku' => ['max:50'],
            'division' => ['required', 'in:HVAC,Consumables'],
            'status' => ['required', 'in:draft,active'],
            'price' => ['numeric'],
            'stock' => ['integer'],
        ];

        if ($validator->validate($_POST, $rules)) {
            $slug = Security::generateSlug($_POST['name']);
            $slugBase = $slug;
            $counter = 1;
            while (Product::slugExists($slug, $action === 'update' ? (int)($_POST['id'] ?? 0) : null)) {
                $slug = $slugBase . '-' . $counter++;
            }

            $data = [
                'name' => Security::sanitize($_POST['name']),
                'slug' => $slug,
                'sku' => Security::sanitize($_POST['sku'] ?? ''),
                'division' => $_POST['division'],
                'category' => Security::sanitize($_POST['category'] ?? ''),
                'description' => Security::sanitizeRich($_POST['description'] ?? ''),
                'meta_description' => Security::sanitize($_POST['meta_description'] ?? ''),
                'specs' => $_POST['specs'] ?? '',
                'price' => (float)($_POST['price'] ?? 0),
                'stock' => (int)($_POST['stock'] ?? 0),
                'unit' => Security::sanitize($_POST['unit'] ?? 'Units'),
                'status' => $_POST['status'],
            ];

            if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $filename = Security::saveUpload($_FILES['image']);
                if ($filename) {
                    $data['image_url'] = $filename;
                }
            }

            if ($action === 'create') {
                $id = Product::create($data);
                $message = 'Product created successfully.';
                $_POST = [];
            } else {
                Product::update((int)$_POST['id'], $data);
                $message = 'Product updated successfully.';
            }
        } else {
            $GLOBALS['errors'] = $validator->errors();
        }
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            Product::delete($id);
            $message = 'Product deleted.';
        }
    }
    }
}

$page = max(1, (int)($_GET['page'] ?? 1));
$division = $_GET['division'] ?? '';
$status = $_GET['status'] ?? '';
$search = Security::sanitize($_GET['search'] ?? '');

$products = Product::paginateAdmin($page, 10, $division, $status, $search);
$editProduct = null;
if (isset($_GET['edit'])) {
    $editProduct = Product::find((int)$_GET['edit']);
}

require_once BASE_PATH . 'includes/admin-header.php';
?>

<?php if ($message): ?>
    <div class="bg-primary-fixed text-deep-royal px-6 py-3 rounded-lg font-body-md" style="background-color: #dbe1ff;"><?= Security::h($message) ?></div>
<?php endif; ?>

<?php if (!empty($GLOBALS['errors'])): ?>
    <div class="bg-error-container text-error px-6 py-3 rounded-lg font-body-md" style="background-color: #ffdad6; color: #ba1a1a;">
        <?php foreach ($GLOBALS['errors'] as $err): ?>
            <p><?= Security::h($err) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="flex justify-between items-end mb-6">
    <div>
        <nav class="flex gap-2 items-center text-on-surface-variant text-sm">
            <span>Admin</span>
            <span class="material-symbols-outlined text-xs">chevron_right</span>
            <span class="text-deep-royal font-medium">Products</span>
        </nav>
    </div>
    <button class="bg-vibrant-amber text-charcoal-text px-6 py-3 rounded-lg font-label-caps flex items-center gap-2 hover:brightness-105 transition-all shadow-sm active:scale-95" onclick="toggleModal('productModal')">
        <span class="material-symbols-outlined">add</span>
        New Entry
    </button>
</div>

<form class="mb-6 flex flex-wrap items-center gap-4" method="GET">
    <div class="relative flex-1 max-w-md">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
        <input class="w-full pl-10 pr-4 py-2 bg-pure-white border border-divider-gray rounded-lg focus:ring-2 focus:ring-deep-royal focus:outline-none transition-all" name="search" placeholder="Search products..." value="<?= Security::h($search) ?>">
    </div>
    <div class="flex items-center gap-2 bg-pure-white px-3 py-1 border border-divider-gray rounded-lg">
        <select class="bg-transparent border-none focus:ring-0 text-sm font-medium text-deep-royal cursor-pointer" name="division" onchange="this.form.submit()">
            <option value="">All Divisions</option>
            <option value="HVAC" <?= selected($division, 'HVAC') ?>>HVAC</option>
            <option value="Consumables" <?= selected($division, 'Consumables') ?>>Consumables</option>
        </select>
    </div>
    <div class="flex items-center gap-2 bg-pure-white px-3 py-1 border border-divider-gray rounded-lg">
        <select class="bg-transparent border-none focus:ring-0 text-sm font-medium text-deep-royal cursor-pointer" name="status" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="active" <?= selected($status, 'active') ?>>Active</option>
            <option value="draft" <?= selected($status, 'draft') ?>>Draft</option>
        </select>
    </div>
</form>

<div class="glass-card rounded-xl overflow-hidden border border-on-surface/5" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(20px);">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-low border-b border-on-surface/10" style="background-color: #f6f3f2;">
                <th class="px-6 py-4 font-label-caps text-on-surface-variant">Product</th>
                <th class="px-6 py-4 font-label-caps text-on-surface-variant">Division</th>
                <th class="px-6 py-4 font-label-caps text-on-surface-variant">Inventory</th>
                <th class="px-6 py-4 font-label-caps text-on-surface-variant">Status</th>
                <th class="px-6 py-4 font-label-caps text-on-surface-variant text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-on-surface/5">
            <?php if (empty($products['items'])): ?>
                <tr><td colspan="5" class="px-6 py-12 text-center text-on-surface-variant">No products found.</td></tr>
            <?php endif; ?>
            <?php foreach ($products['items'] as $product): ?>
            <tr class="hover:bg-surface-bright transition-colors group" style="background: transparent;">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded bg-surface-container flex items-center justify-center overflow-hidden" style="background: #f0eded;">
                            <?php if ($product['image_url']): ?>
                                <img src="<?= uploadUrl($product['image_url']) ?>" class="w-full h-full object-cover" alt="">
                            <?php else: ?>
                                <span class="material-symbols-outlined text-on-surface-variant">inventory_2</span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <p class="font-bold text-deep-royal"><?= Security::h($product['name']) ?></p>
                            <p class="text-xs text-on-surface-variant">SKU: <?= Security::h($product['sku'] ?: 'N/A') ?></p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-full text-xs font-bold" style="background: <?= $product['division'] === 'HVAC' ? '#00236610' : '#FFBF0033' ?>; color: <?= $product['division'] === 'HVAC' ? '#002366' : '#795900' ?>;"><?= Security::h($product['division']) ?></span>
                </td>
                <td class="px-6 py-4 text-sm text-on-surface-variant"><?= (int)$product['stock'] ?> <?= Security::h($product['unit']) ?></td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full" style="background: <?= $product['status'] === 'active' ? '#10b981' : '#f59e0b' ?>;"></span>
                        <span class="text-sm font-medium"><?= ucfirst(Security::h($product['status'])) ?></span>
                    </div>
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                        <a href="?edit=<?= $product['id'] ?>" class="p-2 hover:bg-surface-container rounded-lg text-on-surface-variant transition-colors" onclick="event.preventDefault(); openEdit(<?= $product['id'] ?>, <?= Security::h(json_encode($product)) ?>)">
                            <span class="material-symbols-outlined">edit</span>
                        </a>
                        <form method="POST" class="inline" onsubmit="return confirm('Delete this product?');">
                            <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $product['id'] ?>">
                            <button class="p-2 hover:bg-error/10 rounded-lg transition-colors" style="color: #ba1a1a;">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="px-6 py-4 bg-surface-container-lowest flex justify-between items-center text-sm border-t border-on-surface/5" style="background: #ffffff;">
        <p class="text-on-surface-variant">Showing <span class="font-bold text-deep-royal"><?= count($products['items']) ?></span> of <?= $products['total'] ?> products</p>
        <?php if ($products['totalPages'] > 1): ?>
        <div class="flex gap-2">
            <?php if ($products['hasPrev']): ?>
                <a href="?page=<?= $products['prevPage'] ?>&division=<?= urlencode($division) ?>&status=<?= urlencode($status) ?>&search=<?= urlencode($search) ?>" class="w-8 h-8 flex items-center justify-center rounded border border-divider-gray hover:bg-surface-container transition-colors"><span class="material-symbols-outlined">chevron_left</span></a>
            <?php endif; ?>
            <span class="w-8 h-8 flex items-center justify-center rounded bg-deep-royal text-pure-white font-bold text-sm"><?= $products['page'] ?></span>
            <?php if ($products['hasNext']): ?>
                <a href="?page=<?= $products['nextPage'] ?>&division=<?= urlencode($division) ?>&status=<?= urlencode($status) ?>&search=<?= urlencode($search) ?>" class="w-8 h-8 flex items-center justify-center rounded border border-divider-gray hover:bg-surface-container transition-colors"><span class="material-symbols-outlined">chevron_right</span></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="fixed inset-0 z-[60] hidden" id="productModal">
    <div class="absolute inset-0 bg-deep-royal/20 backdrop-blur-sm" onclick="toggleModal('productModal')"></div>
    <div class="absolute right-0 top-0 h-full w-full max-w-2xl bg-pure-white shadow-2xl flex flex-col transform transition-transform duration-300 translate-x-full" id="modalContainer">
        <div class="px-8 py-6 border-b border-on-surface/10 flex justify-between items-center">
            <h3 class="font-headline-sm text-headline-sm text-deep-royal" id="modalTitle">Add New Product</h3>
            <button class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-surface-container transition-colors" onclick="toggleModal('productModal')"><span class="material-symbols-outlined">close</span></button>
        </div>
        <div class="flex-1 overflow-y-auto p-8 hide-scrollbar">
            <form method="POST" enctype="multipart/form-data" id="productForm">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="productId" value="">
                <div class="space-y-6">
                    <section class="space-y-4">
                        <h4 class="font-label-caps text-on-surface-variant border-b border-divider-gray pb-2" style="color: #444650; font-size: 12px; letter-spacing: 0.05em; text-transform: uppercase;">Basic Information</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Product Name</label>
                                <input class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="name" id="inputName" value="<?= old('name') ?>" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">SKU</label>
                                <input class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="sku" id="inputSku" value="<?= old('sku') ?>">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Unit</label>
                                <input class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="unit" id="inputUnit" value="<?= old('unit', 'Units') ?>">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Division</label>
                                <select class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="division" id="inputDivision">
                                    <option value="HVAC">HVAC</option>
                                    <option value="Consumables">Consumables</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Status</label>
                                <select class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="status" id="inputStatus">
                                    <option value="draft">Draft</option>
                                    <option value="active">Active</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Price</label>
                                <input class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="price" id="inputPrice" type="number" step="0.01" value="<?= old('price', '0') ?>">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Stock</label>
                                <input class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="stock" id="inputStock" type="number" value="<?= old('stock', '0') ?>">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Description</label>
                                <textarea class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="description" id="inputDescription" rows="4"><?= old('description') ?></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Category</label>
                                <select class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="category" id="inputCategory">
                                    <option value="">Select category</option>
                                    <?php foreach (Category::asOptions() as $cat): ?>
                                        <option value="<?= Security::h($cat['name']) ?>"><?= Security::h($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Meta Description (SEO)</label>
                                <input class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="meta_description" id="inputMetaDesc" maxlength="300">
                            </div>
                        </div>
                    </section>
                    <section class="space-y-4">
                        <div class="flex justify-between items-center border-b border-divider-gray pb-2">
                            <h4 class="font-label-caps text-on-surface-variant" style="color: #444650;">Technical Specifications</h4>
                            <button type="button" class="text-xs text-vibrant-amber font-bold flex items-center gap-1 hover:underline" onclick="addSpecRow()">
                                <span class="material-symbols-outlined" style="font-size: 16px;">add</span> Add Row
                            </button>
                        </div>
                        <div id="specsContainer" class="space-y-2">
                            <div class="text-sm text-on-surface-variant text-center py-4" id="specsEmpty">No specifications added yet.</div>
                        </div>
                        <textarea name="specs" id="inputSpecs" class="hidden"><?= old('specs') ?></textarea>
                    </section>
                    <section class="space-y-4">
                        <h4 class="font-label-caps text-on-surface-variant border-b border-divider-gray pb-2" style="color: #444650;">Product Image</h4>
                        <div class="border-2 border-dashed border-divider-gray rounded-xl p-8 text-center hover:border-deep-royal/30 transition-colors cursor-pointer group" onclick="document.getElementById('imageInput').click()">
                            <span class="material-symbols-outlined text-4xl text-on-surface-variant group-hover:text-deep-royal mb-2">cloud_upload</span>
                            <p class="text-sm font-medium">Click to upload or drag and drop</p>
                            <p class="text-xs text-on-surface-variant mt-1">JPEG, PNG, WebP (max 2MB)</p>
                            <input type="file" name="image" id="imageInput" class="hidden" accept="image/jpeg,image/png,image/webp">
                        </div>
                    </section>
                </div>
                <div class="px-8 py-6 border-t border-on-surface/10 bg-surface-container-lowest flex justify-end gap-4 mt-6" style="margin-left: -2rem; margin-right: -2rem; margin-bottom: -2rem; background: #ffffff;">
                    <button type="button" class="px-6 py-3 rounded-lg font-label-caps border border-divider-gray hover:bg-surface-container transition-all" onclick="toggleModal('productModal')">Cancel</button>
                    <button type="submit" class="bg-deep-royal text-pure-white px-8 py-3 rounded-lg font-label-caps hover:brightness-110 transition-all shadow-md active:scale-95">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function addSpecRow(key, value) {
    var container = document.getElementById('specsContainer');
    var empty = document.getElementById('specsEmpty');
    if (empty) empty.remove();
    var row = document.createElement('div');
    row.className = 'specs-row flex gap-2 items-center';
    row.innerHTML =
        '<input type="text" placeholder="Key" value="' + (key || '') + '" class="specs-key flex-1 border border-divider-gray rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-deep-royal focus:outline-none bg-pure-white" style="max-width: 180px;">' +
        '<input type="text" placeholder="Value" value="' + (value || '') + '" class="specs-value flex-1 border border-divider-gray rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-deep-royal focus:outline-none bg-pure-white">' +
        '<button type="button" class="p-2 hover:bg-error/10 rounded-lg text-on-surface-variant hover:text-error transition-colors" onclick="removeSpecRow(this)"><span class="material-symbols-outlined" style="font-size: 18px;">close</span></button>';
    container.appendChild(row);
}

function removeSpecRow(btn) {
    var row = btn.closest('.specs-row');
    row.remove();
    var container = document.getElementById('specsContainer');
    if (container.querySelectorAll('.specs-row').length === 0) {
        var empty = document.createElement('div');
        empty.id = 'specsEmpty';
        empty.className = 'text-sm text-on-surface-variant text-center py-4';
        empty.textContent = 'No specifications added yet.';
        container.appendChild(empty);
    }
}

function specsToJson() {
    var rows = document.querySelectorAll('.specs-row');
    var obj = {};
    rows.forEach(function(row) {
        var key = row.querySelector('.specs-key').value.trim();
        var val = row.querySelector('.specs-value').value.trim();
        if (key) obj[key] = val;
    });
    return Object.keys(obj).length ? JSON.stringify(obj, null, 2) : '';
}

function specsFromJson(jsonStr) {
    var container = document.getElementById('specsContainer');
    container.querySelectorAll('.specs-row').forEach(function(r) { r.remove(); });
    var empty = document.getElementById('specsEmpty');
    if (empty) empty.remove();
    if (!jsonStr) {
        var div = document.createElement('div');
        div.id = 'specsEmpty';
        div.className = 'text-sm text-on-surface-variant text-center py-4';
        div.textContent = 'No specifications added yet.';
        container.appendChild(div);
        return;
    }
    try {
        var obj = JSON.parse(jsonStr);
        for (var key in obj) {
            if (obj.hasOwnProperty(key)) addSpecRow(key, obj[key]);
        }
    } catch(e) {
        addSpecRow('', '');
    }
}

function toggleModal(id) {
    const modal = document.getElementById(id);
    const container = document.getElementById('modalContainer');
    if (modal.classList.contains('hidden')) {
        modal.classList.remove('hidden');
        setTimeout(() => {
            container.classList.remove('translate-x-full');
            container.classList.add('translate-x-0');
        }, 10);
    } else {
        container.classList.remove('translate-x-0');
        container.classList.add('translate-x-full');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }
}

function openEdit(id, product) {
    document.getElementById('formAction').value = 'update';
    document.getElementById('productId').value = id;
    document.getElementById('modalTitle').textContent = 'Edit Product';
    document.getElementById('inputName').value = product.name;
    document.getElementById('inputSku').value = product.sku || '';
    document.getElementById('inputUnit').value = product.unit || 'Units';
    document.getElementById('inputDivision').value = product.division;
    document.getElementById('inputStatus').value = product.status;
    document.getElementById('inputPrice').value = product.price || 0;
    document.getElementById('inputStock').value = product.stock || 0;
    document.getElementById('inputDescription').value = product.description || '';
    document.getElementById('inputCategory').value = product.category || '';
    document.getElementById('inputMetaDesc').value = product.meta_description || '';
    document.getElementById('inputSpecs').value = product.specs || '';
    specsFromJson(product.specs || '');
    toggleModal('productModal');
}

document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('productForm');
    if (form) {
        form.addEventListener('submit', function() {
            document.getElementById('inputSpecs').value = specsToJson();
        });
    }
});

<?php if ($editProduct): ?>
document.addEventListener('DOMContentLoaded', function() {
    openEdit(<?= $editProduct['id'] ?>, <?= json_encode($editProduct) ?>);
});
<?php endif; ?>
</script>

<?php require_once BASE_PATH . 'includes/admin-footer.php'; ?>
