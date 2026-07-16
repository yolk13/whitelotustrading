<?php

$pageTitle = 'Page Management';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
        $message = 'Invalid security token. Please try again.';
        $GLOBALS['errors']['csrf'] = $message;
    } else {
    $action = $_POST['action'] ?? '';

    if ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $validator = new Validator();
        if ($validator->validate($_POST, [
            'title' => ['required', 'min:2', 'max:200'],
            'slug' => ['required', 'min:2', 'max:200'],
            'status' => ['required', 'in:draft,published'],
        ])) {
            $data = [
                'title' => Security::sanitize($_POST['title']),
                'slug' => Security::sanitize($_POST['slug']),
                'content' => Security::sanitizeRich($_POST['content'] ?? ''),
                'meta_description' => Security::sanitize($_POST['meta_description'] ?? ''),
                'status' => $_POST['status'],
            ];
            $old = Page::find($id);
            Page::update($id, $data);
            Audit::log('update', 'page', $id, Audit::diff($old ?? [], $data));
            $message = 'Page updated successfully.';
        } else {
            $GLOBALS['errors'] = $validator->errors();
        }
    }
    }
}

$pages = Page::where("slug != 'home'", [], 'title ASC');

require_once BASE_PATH . 'includes/admin-header.php';
?>

<?php if ($message): ?>
    <div class="bg-primary-fixed text-deep-royal px-6 py-3 rounded-lg font-body-md mb-6" style="background-color: #dbe1ff;"><?= Security::h($message) ?></div>
<?php endif; ?>

<?php if (!empty($GLOBALS['errors'])): ?>
    <div class="bg-error-container text-error px-6 py-3 rounded-lg font-body-md mb-6" style="background-color: #ffdad6; color: #ba1a1a;">
        <?php foreach ($GLOBALS['errors'] as $err): ?>
            <p><?= Security::h($err) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="glass-card rounded-xl overflow-x-auto border border-on-surface/5" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(20px);">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-low border-b border-on-surface/10">
                <th class="px-6 py-4 font-label-caps text-on-surface-variant">Page</th>
                <th class="px-6 py-4 font-label-caps text-on-surface-variant">Content</th>
                <th class="px-6 py-4 font-label-caps text-on-surface-variant">Status</th>
                <th class="px-6 py-4 font-label-caps text-on-surface-variant text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-on-surface/5">
            <?php foreach ($pages as $page): ?>
            <tr class="hover:bg-surface-bright transition-colors">
                <td class="px-6 py-4">
                    <p class="font-bold text-deep-royal"><?= Security::h($page['title']) ?></p>
                    <p class="text-xs text-on-surface-variant">/<?= Security::h($page['slug']) ?></p>
                </td>
                <td class="px-6 py-4">
                    <p class="text-sm text-on-surface-variant truncate max-w-[300px]" title="<?= Security::h(strip_tags($page['content'] ?? '')) ?>"><?= Security::h(mb_substr(strip_tags($page['content'] ?? ''), 0, 120)) ?><?= mb_strlen(strip_tags($page['content'] ?? '')) > 120 ? '...' : '' ?></p>
                </td>
                <td class="px-6 py-4">
                    <span class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full" style="background: <?= $page['status'] === 'published' ? '#10b981' : '#f59e0b' ?>;"></span>
                        <span class="text-sm font-medium"><?= ucfirst($page['status']) ?></span>
                    </span>
                </td>
                <td class="px-6 py-4 text-right">
                    <button type="button" onclick="openEditModal(<?= (int)$page['id'] ?>)" class="p-2 hover:bg-surface-container rounded-lg text-on-surface-variant transition-colors inline-block cursor-pointer">
                        <span class="material-symbols-outlined">edit</span>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="pageModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/40" style="backdrop-filter: blur(4px);">
    <div class="bg-pure-white rounded-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto p-6 shadow-xl relative mx-4">
        <button type="button" onclick="closeEditModal()" class="absolute top-4 right-4 p-2 hover:bg-surface-container rounded-lg text-on-surface-variant transition-colors cursor-pointer">
            <span class="material-symbols-outlined">close</span>
        </button>
        <h3 class="font-headline-sm text-headline-sm text-deep-royal mb-6" id="modalTitle">Edit Page</h3>
        <form method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" id="modalCsrf" value="<?= Security::generateCsrfToken() ?>">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="modalPageId" value="0">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Title</label>
                    <input id="modalTitleInput" class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="title" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Slug</label>
                    <input id="modalSlug" class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="slug" required>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Meta Description</label>
                <input id="modalMeta" class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="meta_description">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Status</label>
                    <select id="modalStatus" class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="status">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Last Updated</label>
                    <p id="modalUpdated" class="px-3 py-3 text-on-surface-variant text-sm"></p>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Content</label>
                <textarea id="page-editor" class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="content" rows="20"></textarea>
            </div>
            <div class="flex gap-4">
                <button type="submit" class="bg-deep-royal text-pure-white px-8 py-3 rounded-lg font-label-caps hover:brightness-110 transition-all shadow-md">Save Changes</button>
                <button type="button" onclick="closeEditModal()" class="px-6 py-3 rounded-lg font-label-caps border border-divider-gray hover:bg-surface-container transition-all cursor-pointer">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
<script>
const pagesData = <?= json_encode($pages) ?>;

function openEditModal(id) {
    const page = pagesData.find(function(p) { return p.id === id; });
    if (!page) return;

    document.getElementById('modalPageId').value = page.id;
    document.getElementById('modalTitle').textContent = 'Edit: ' + page.title;
    document.getElementById('modalTitleInput').value = page.title;
    document.getElementById('modalSlug').value = page.slug;
    document.getElementById('modalMeta').value = page.meta_description || '';
    document.getElementById('modalStatus').value = page.status || 'draft';
    document.getElementById('modalUpdated').textContent = page.updated_at || '';

    document.getElementById('pageModal').classList.remove('hidden');

    if (tinymce.get('page-editor')) {
        tinymce.get('page-editor').remove();
    }
    setTimeout(function() {
        tinymce.init({
            selector: '#page-editor',
            min_height: 400,
            height: 'auto',
            menubar: true,
            plugins: 'link image code table lists advlist',
            toolbar: 'undo redo | blocks | bold italic underline strikethrough | alignleft aligncenter alignright | bullist numlist outdent indent | link image | table | code | removeformat',
            images_upload_url: '/admin/upload-image',
            images_upload_handler: function(blobInfo, progress) {
                return new Promise(function(resolve, reject) {
                    var xhr = new XMLHttpRequest();
                    xhr.withCredentials = false;
                    xhr.open('POST', '/admin/upload-image');
                    var formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());
                    var csrfMeta = document.querySelector('input[name="csrf_token"]');
                    if (csrfMeta) formData.append('csrf_token', csrfMeta.value);
                    xhr.onload = function() {
                        if (xhr.status !== 200) {
                            reject('Upload failed: ' + xhr.status);
                            return;
                        }
                        var json = JSON.parse(xhr.responseText);
                        if (!json || typeof json.location !== 'string') {
                            reject('Invalid upload response');
                            return;
                        }
                        resolve(json.location);
                    };
                    xhr.onerror = function() {
                        reject('Upload error');
                    };
                    xhr.send(formData);
                });
            },
            content_style: 'body { font-family: system-ui, sans-serif; font-size: 16px; line-height: 1.6; color: #1a1a2e; }',
            valid_elements: '*[*]',
            extended_valid_elements: 'span[*],div[*],section[*],img[*]',
            setup: function(editor) {
                editor.on('init', function() {
                    editor.setContent(page.content || '');
                });
            }
        });
    }, 200);
}

function closeEditModal() {
    var editor = tinymce.get('page-editor');
    if (editor) {
        editor.remove();
    }
    document.getElementById('pageModal').classList.add('hidden');
}
</script>
<?php require_once BASE_PATH . 'includes/admin-footer.php'; ?>
