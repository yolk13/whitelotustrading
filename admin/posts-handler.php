<?php

$pageTitle = 'Blog Management';
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
            'title' => ['required', 'min:3', 'max:200'],
            'category' => ['required'],
            'status' => ['required', 'in:draft,published'],
        ];

        if ($validator->validate($_POST, $rules)) {
            $slug = Security::generateSlug($_POST['title']);
            $slugBase = $slug;
            $counter = 1;
            while (Post::slugExists($slug, $action === 'update' ? (int)($_POST['id'] ?? 0) : null)) {
                $slug = $slugBase . '-' . $counter++;
            }

            $data = [
                'title' => Security::sanitize($_POST['title']),
                'slug' => $slug,
                'content' => Security::sanitizeRich($_POST['content'] ?? ''),
                'excerpt' => Security::sanitize($_POST['excerpt'] ?? ''),
                'category' => Security::sanitize($_POST['category']),
                'status' => $_POST['status'],
            ];

            if ($_POST['status'] === 'published' && $action === 'create') {
                $data['published_at'] = date('Y-m-d H:i:s');
            }

            if (!empty($_FILES['featured_image']['name']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
                $filename = Security::saveUpload($_FILES['featured_image']);
                if ($filename) {
                    $data['featured_image'] = $filename;
                }
            }

            if ($action === 'create') {
                $id = Post::create($data);
                $message = 'Post created successfully.';
                $_POST = [];
            } else {
                if ($_POST['status'] === 'published') {
                    $existing = Post::find((int)$_POST['id']);
                    if ($existing && !$existing['published_at']) {
                        $data['published_at'] = date('Y-m-d H:i:s');
                    }
                }
                Post::update((int)$_POST['id'], $data);
                $message = 'Post updated successfully.';
            }
        } else {
            $GLOBALS['errors'] = $validator->errors();
        }
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            Post::delete($id);
            $message = 'Post deleted.';
        }
    }
    }
}

$page = max(1, (int)($_GET['page'] ?? 1));
$status = $_GET['status'] ?? '';
$category = $_GET['category'] ?? '';

$posts = Post::paginateAdmin($page, 10, $status, $category);
$editPost = null;
if (isset($_GET['edit'])) {
    $editPost = Post::find((int)$_GET['edit']);
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
            <span class="text-deep-royal font-medium">Blog</span>
        </nav>
    </div>
    <button class="bg-vibrant-amber text-charcoal-text px-6 py-3 rounded-lg font-label-caps flex items-center gap-2 hover:brightness-105 transition-all shadow-sm active:scale-95" onclick="toggleModal('postModal')">
        <span class="material-symbols-outlined">add</span>
        New Post
    </button>
</div>

<form class="mb-6 flex flex-wrap items-center gap-4" method="GET">
    <div class="flex items-center gap-2 bg-pure-white px-3 py-1 border border-divider-gray rounded-lg">
        <select class="bg-transparent border-none focus:ring-0 text-sm font-medium text-deep-royal cursor-pointer" name="status" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="published" <?= selected($status, 'published') ?>>Published</option>
            <option value="draft" <?= selected($status, 'draft') ?>>Draft</option>
        </select>
    </div>
    <div class="flex items-center gap-2 bg-pure-white px-3 py-1 border border-divider-gray rounded-lg">
        <select class="bg-transparent border-none focus:ring-0 text-sm font-medium text-deep-royal cursor-pointer" name="category" onchange="this.form.submit()">
            <option value="">All Categories</option>
            <option value="Company" <?= selected($category, 'Company') ?>>Company</option>
            <option value="Industry News" <?= selected($category, 'Industry News') ?>>Industry News</option>
            <option value="Wellness" <?= selected($category, 'Wellness') ?>>Wellness</option>
        </select>
    </div>
</form>

<div class="glass-card rounded-xl overflow-hidden border border-on-surface/5" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(20px);">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-low border-b border-on-surface/10" style="background-color: #f6f3f2;">
                <th class="px-6 py-4 font-label-caps text-on-surface-variant">Title</th>
                <th class="px-6 py-4 font-label-caps text-on-surface-variant">Category</th>
                <th class="px-6 py-4 font-label-caps text-on-surface-variant">Status</th>
                <th class="px-6 py-4 font-label-caps text-on-surface-variant">Date</th>
                <th class="px-6 py-4 font-label-caps text-on-surface-variant text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-on-surface/5">
            <?php if (empty($posts['items'])): ?>
                <tr><td colspan="5" class="px-6 py-12 text-center text-on-surface-variant">No posts found.</td></tr>
            <?php endif; ?>
            <?php foreach ($posts['items'] as $post): ?>
            <tr class="hover:bg-surface-bright transition-colors group">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded bg-surface-container flex items-center justify-center overflow-hidden" style="background: #f0eded;">
                            <?php if ($post['featured_image']): ?>
                                <img src="<?= uploadUrl($post['featured_image']) ?>" class="w-full h-full object-cover" alt="">
                            <?php else: ?>
                                <span class="material-symbols-outlined text-on-surface-variant" style="font-size: 20px;">article</span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <p class="font-bold text-deep-royal"><?= Security::h($post['title']) ?></p>
                            <p class="text-xs text-on-surface-variant">/blog/<?= Security::h($post['slug']) ?></p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-full text-xs font-bold" style="background: <?= $post['category'] === 'Wellness' ? '#FFBF0033' : ($post['category'] === 'Industry News' ? '#00236610' : '#f0eded') ?>; color: <?= $post['category'] === 'Wellness' ? '#795900' : ($post['category'] === 'Industry News' ? '#002366' : '#444650') ?>;"><?= Security::h($post['category']) ?></span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full" style="background: <?= $post['status'] === 'published' ? '#10b981' : '#f59e0b' ?>;"></span>
                        <span class="text-sm font-medium"><?= ucfirst(Security::h($post['status'])) ?></span>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm text-on-surface-variant"><?= $post['published_at'] ? formatDate($post['published_at'], 'M d, Y') : '—' ?></td>
                <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                        <a href="/blog/<?= Security::h($post['slug']) ?>" target="_blank" class="p-2 hover:bg-surface-container rounded-lg text-on-surface-variant transition-colors">
                            <span class="material-symbols-outlined">visibility</span>
                        </a>
                        <a href="?edit=<?= $post['id'] ?>" class="p-2 hover:bg-surface-container rounded-lg text-on-surface-variant transition-colors" onclick="event.preventDefault(); openEditPost(<?= $post['id'] ?>, <?= Security::h(json_encode($post)) ?>)">
                            <span class="material-symbols-outlined">edit</span>
                        </a>
                        <form method="POST" class="inline" onsubmit="return confirm('Delete this post?');">
                            <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $post['id'] ?>">
                            <button class="p-2 hover:bg-error/10 rounded-lg transition-colors" style="color: #ba1a1a;"><span class="material-symbols-outlined">delete</span></button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="px-6 py-4 bg-surface-container-lowest flex justify-between items-center text-sm border-t border-on-surface/5" style="background: #ffffff;">
        <p class="text-on-surface-variant">Showing <?= count($posts['items']) ?> of <?= $posts['total'] ?> posts</p>
        <?php if ($posts['totalPages'] > 1): ?>
        <div class="flex gap-2">
            <?php if ($posts['hasPrev']): ?>
                <a href="?page=<?= $posts['prevPage'] ?>&status=<?= urlencode($status) ?>&category=<?= urlencode($category) ?>" class="w-8 h-8 flex items-center justify-center rounded border border-divider-gray hover:bg-surface-container transition-colors"><span class="material-symbols-outlined">chevron_left</span></a>
            <?php endif; ?>
            <span class="w-8 h-8 flex items-center justify-center rounded bg-deep-royal text-pure-white font-bold text-sm"><?= $posts['page'] ?></span>
            <?php if ($posts['hasNext']): ?>
                <a href="?page=<?= $posts['nextPage'] ?>&status=<?= urlencode($status) ?>&category=<?= urlencode($category) ?>" class="w-8 h-8 flex items-center justify-center rounded border border-divider-gray hover:bg-surface-container transition-colors"><span class="material-symbols-outlined">chevron_right</span></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="fixed inset-0 z-[60] hidden" id="postModal">
    <div class="absolute inset-0 bg-deep-royal/20 backdrop-blur-sm" onclick="toggleModal('postModal')"></div>
    <div class="absolute right-0 top-0 h-full w-full max-w-2xl bg-pure-white shadow-2xl flex flex-col transform transition-transform duration-300 translate-x-full" id="postModalContainer">
        <div class="px-8 py-6 border-b border-on-surface/10 flex justify-between items-center">
            <h3 class="font-headline-sm text-headline-sm text-deep-royal" id="postModalTitle">New Blog Post</h3>
            <button class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-surface-container transition-colors" onclick="toggleModal('postModal')"><span class="material-symbols-outlined">close</span></button>
        </div>
        <div class="flex-1 overflow-y-auto p-8 hide-scrollbar">
            <form method="POST" enctype="multipart/form-data" id="postForm">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
                <input type="hidden" name="action" id="postFormAction" value="create">
                <input type="hidden" name="id" id="postId" value="">
                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Title</label>
                        <input class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="title" id="postInputTitle" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Category</label>
                            <select class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="category" id="postInputCategory">
                                <option value="Company">Company</option>
                                <option value="Industry News">Industry News</option>
                                <option value="Wellness">Wellness</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Status</label>
                            <select class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="status" id="postInputStatus">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Excerpt</label>
                        <textarea class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="excerpt" id="postInputExcerpt" rows="2" maxlength="300"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Content</label>
                        <textarea name="content" id="postInputContent" rows="15"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Featured Image</label>
                        <div class="border-2 border-dashed border-divider-gray rounded-xl p-8 text-center hover:border-deep-royal/30 transition-colors cursor-pointer group" onclick="document.getElementById('postImageInput').click()">
                            <span class="material-symbols-outlined text-4xl text-on-surface-variant group-hover:text-deep-royal mb-2">image</span>
                            <p class="text-sm font-medium">Click to upload</p>
                            <input type="file" name="featured_image" id="postImageInput" class="hidden" accept="image/jpeg,image/png,image/webp">
                        </div>
                    </div>
                </div>
                <div class="px-8 py-6 border-t border-on-surface/10 bg-surface-container-lowest flex justify-end gap-4 mt-6" style="margin-left: -2rem; margin-right: -2rem; margin-bottom: -2rem; background: #ffffff;">
                    <button type="button" class="px-6 py-3 rounded-lg font-label-caps border border-divider-gray hover:bg-surface-container transition-all" onclick="toggleModal('postModal')">Cancel</button>
                    <button type="submit" class="bg-deep-royal text-pure-white px-8 py-3 rounded-lg font-label-caps hover:brightness-110 transition-all shadow-md active:scale-95">Save Post</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
<script>
var blogEditorInitialized = false;

function initBlogEditor() {
    if (blogEditorInitialized) return;
    blogEditorInitialized = true;
    tinymce.init({
        selector: '#postInputContent',
        height: 400,
        menubar: false,
        plugins: 'link image code table lists advlist',
        toolbar: 'undo redo | blocks | bold italic underline | bullist numlist outdent indent | link image | table | code | removeformat',
        images_upload_url: '/admin/upload-image',
        images_upload_handler: function(blobInfo, progress) {
            return new Promise(function(resolve, reject) {
                var xhr = new XMLHttpRequest();
                xhr.withCredentials = false;
                xhr.open('POST', '/admin/upload-image');
                var formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                var csrfMeta = document.querySelector('#postForm input[name="csrf_token"]');
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
                xhr.onerror = function() { reject('Upload error'); };
                xhr.send(formData);
            });
        },
        content_style: 'body { font-family: system-ui, sans-serif; font-size: 16px; line-height: 1.6; color: #1a1a2e; }',
        valid_elements: '*[*]',
        extended_valid_elements: 'span[*],div[*],img[*]',
        setup: function(editor) {
            editor.on('open', function() {
                document.querySelectorAll('.tox-tinymce').forEach(function(el) {
                    el.style.width = '100%';
                });
            });
        }
    });
}

function toggleModal(id) {
    const containerId = id === 'postModal' ? 'postModalContainer' : 'modalContainer';
    const modal = document.getElementById(id);
    const container = document.getElementById(containerId);
    if (modal.classList.contains('hidden')) {
        modal.classList.remove('hidden');
        initBlogEditor();
        setTimeout(function() {
            container.classList.remove('translate-x-full');
            container.classList.add('translate-x-0');
        }, 10);
    } else {
        container.classList.remove('translate-x-0');
        container.classList.add('translate-x-full');
        setTimeout(function() { modal.classList.add('hidden'); }, 300);
    }
}

function openEditPost(id, post) {
    document.getElementById('postFormAction').value = 'update';
    document.getElementById('postId').value = id;
    document.getElementById('postModalTitle').textContent = 'Edit Post';
    document.getElementById('postInputTitle').value = post.title;
    document.getElementById('postInputCategory').value = post.category || 'Company';
    document.getElementById('postInputStatus').value = post.status;
    document.getElementById('postInputExcerpt').value = post.excerpt || '';
    if (blogEditorInitialized) {
        tinymce.get('postInputContent').setContent(post.content || '');
    } else {
        document.getElementById('postInputContent').value = post.content || '';
    }
    toggleModal('postModal');
}

<?php if ($editPost): ?>
document.addEventListener('DOMContentLoaded', function() {
    openEditPost(<?= $editPost['id'] ?>, <?= json_encode($editPost) ?>);
});
<?php endif; ?>
</script>

<?php require_once BASE_PATH . 'includes/admin-footer.php'; ?>
