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
            Page::update($id, [
                'title' => Security::sanitize($_POST['title']),
                'slug' => Security::sanitize($_POST['slug']),
                'content' => Security::sanitizeRich($_POST['content'] ?? ''),
                'meta_description' => Security::sanitize($_POST['meta_description'] ?? ''),
                'status' => $_POST['status'],
            ]);
            $message = 'Page updated successfully.';
        } else {
            $GLOBALS['errors'] = $validator->errors();
        }
    }
    }
}

$editPage = null;
if (isset($_GET['edit'])) {
    $editPage = Page::find((int)$_GET['edit']);
}

$pages = Page::all('title ASC');

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

<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
    <div class="lg:col-span-5">
        <div class="glass-card rounded-xl overflow-hidden border border-on-surface/5" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(20px);">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low border-b border-on-surface/10">
                        <th class="px-6 py-4 font-label-caps text-on-surface-variant">Page</th>
                        <th class="px-6 py-4 font-label-caps text-on-surface-variant">Status</th>
                        <th class="px-6 py-4 font-label-caps text-on-surface-variant text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-on-surface/5">
                    <?php foreach ($pages as $page): ?>
                    <tr class="hover:bg-surface-bright transition-colors <?= ($editPage && $editPage['id'] === $page['id']) ? 'bg-primary-container/5' : '' ?>">
                        <td class="px-6 py-4">
                            <p class="font-bold text-deep-royal"><?= Security::h($page['title']) ?></p>
                            <p class="text-xs text-on-surface-variant">/<?= Security::h($page['slug']) ?></p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full" style="background: <?= $page['status'] === 'published' ? '#10b981' : '#f59e0b' ?>;"></span>
                                <span class="text-sm font-medium"><?= ucfirst($page['status']) ?></span>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="?edit=<?= $page['id'] ?>" class="p-2 hover:bg-surface-container rounded-lg text-on-surface-variant transition-colors inline-block">
                                <span class="material-symbols-outlined">edit</span>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="lg:col-span-7">
        <?php if ($editPage): ?>
        <div class="glass-card rounded-xl border border-on-surface/5 p-8" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(20px);">
            <h3 class="font-headline-sm text-headline-sm text-deep-royal mb-6">Edit: <?= Security::h($editPage['title']) ?></h3>
            <form method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?= $editPage['id'] ?>">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Title</label>
                        <input class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="title" value="<?= Security::h($editPage['title']) ?>" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Slug</label>
                        <input class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="slug" value="<?= Security::h($editPage['slug']) ?>" required>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Meta Description</label>
                    <input class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="meta_description" value="<?= Security::h($editPage['meta_description'] ?? '') ?>">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Status</label>
                        <select class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="status">
                            <option value="draft" <?= selected($editPage['status'], 'draft') ?>>Draft</option>
                            <option value="published" <?= selected($editPage['status'], 'published') ?>>Published</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Last Updated</label>
                        <p class="px-3 py-3 text-on-surface-variant text-sm"><?= formatDate($editPage['updated_at']) ?></p>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Content</label>
                    <textarea id="page-editor" class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="content" rows="20"><?= Security::h($editPage['content']) ?></textarea>
                </div>
                <div class="flex gap-4">
                    <button type="submit" class="bg-deep-royal text-pure-white px-8 py-3 rounded-lg font-label-caps hover:brightness-110 transition-all shadow-md">Save Changes</button>
                    <a href="/admin/pages" class="px-6 py-3 rounded-lg font-label-caps border border-divider-gray hover:bg-surface-container transition-all">Cancel</a>
                </div>
            </form>
        </div>
        <?php else: ?>
            <div class="glass-card rounded-xl border border-on-surface/5 p-12 text-center" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(20px);">
                <span class="material-symbols-outlined text-6xl text-on-surface-variant/30 mb-4">edit_note</span>
                <h3 class="font-headline-sm text-headline-sm text-deep-royal mb-2">Select a page to edit</h3>
                <p class="font-body-md text-body-md text-on-surface-variant">Choose a page from the list on the left to modify its content.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editor = document.getElementById('page-editor');
    if (editor) {
        tinymce.init({
            target: editor,
            height: 600,
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
        });
    }
});
</script>
<?php require_once BASE_PATH . 'includes/admin-footer.php'; ?>
