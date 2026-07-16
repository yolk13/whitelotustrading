<?php

Auth::requireRole('admin');
$pageTitle = 'User Management';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
        $message = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'create' || $action === 'update') {
            $id = (int)($_POST['id'] ?? 0);
            $username = Security::sanitize($_POST['username'] ?? '');
            $email = Security::sanitize($_POST['email'] ?? '');
            $displayName = Security::sanitize($_POST['display_name'] ?? '');
            $role = $_POST['role'] ?? 'editor';
            $password = $_POST['password'] ?? '';

            if ($username && $email) {
                if ($action === 'create') {
                    if (Database::exists('users', 'username', $username)) {
                        $message = 'Username already exists.';
                    } elseif (Database::exists('users', 'email', $email)) {
                        $message = 'Email already exists.';
                    } elseif (strlen($password) < 6) {
                        $message = 'Password must be at least 6 characters.';
                    } else {
                        Database::insert('users', [
                            'username' => $username,
                            'email' => $email,
                            'display_name' => $displayName ?: $username,
                            'password_hash' => Security::hashPassword($password),
                            'role' => $role,
                        ]);
                        Audit::log('create', 'user', (int)Database::getInstance()->lastInsertId(), json_encode(['username' => $username, 'role' => $role]));
                        $message = 'User created.';
                    }
                } else {
                    $data = [
                        'username' => $username,
                        'email' => $email,
                        'display_name' => $displayName ?: $username,
                        'role' => $role,
                    ];
                    if ($password) {
                        $data['password_hash'] = Security::hashPassword($password);
                    }
                    Database::update('users', $data, 'id = ?', [$id]);
                    Audit::log('update', 'user', $id, json_encode(['username' => $username]));
                    $message = 'User updated.';
                }
            } else {
                $message = 'Username and email are required.';
            }
        }

        if ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id && $id !== Auth::id()) {
                Database::delete('users', 'id = ?', [$id]);
                $message = 'User deleted.';
            } else {
                $message = 'Cannot delete yourself.';
            }
        }
    }
}

$users = Database::fetchAll("SELECT id, username, email, display_name, role, last_login, created_at FROM users ORDER BY role, username");

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
            <span class="text-deep-royal font-medium">Users</span>
        </nav>
    </div>
    <button class="bg-vibrant-amber text-charcoal-text px-6 py-3 rounded-lg font-label-caps flex items-center gap-2 hover:brightness-105 transition-all shadow-sm" onclick="toggleForm()">
        <span class="material-symbols-outlined">add</span> New User
    </button>
</div>

<div id="userForm" class="glass-card rounded-xl border border-on-surface/5 p-6 mb-6 hidden">
    <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
        <input type="hidden" name="action" id="userFormAction" value="create">
        <input type="hidden" name="id" id="userId" value="0">
        <div>
            <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Username</label>
            <input class="w-full border border-divider-gray rounded-lg p-2 text-sm focus:ring-2 focus:ring-deep-royal focus:outline-none" name="username" id="inputUsername" required>
        </div>
        <div>
            <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Email</label>
            <input class="w-full border border-divider-gray rounded-lg p-2 text-sm focus:ring-2 focus:ring-deep-royal focus:outline-none" name="email" id="inputEmail" type="email" required>
        </div>
        <div>
            <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Display Name</label>
            <input class="w-full border border-divider-gray rounded-lg p-2 text-sm focus:ring-2 focus:ring-deep-royal focus:outline-none" name="display_name" id="inputDisplayName">
        </div>
        <div>
            <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Role</label>
            <select class="w-full border border-divider-gray rounded-lg p-2 text-sm focus:ring-2 focus:ring-deep-royal focus:outline-none" name="role" id="inputRole">
                <option value="admin">Super Admin</option>
                <option value="editor">Editor</option>
                <option value="support">Support</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Password</label>
            <input class="w-full border border-divider-gray rounded-lg p-2 text-sm focus:ring-2 focus:ring-deep-royal focus:outline-none" name="password" id="inputPassword" type="password" placeholder="Min 6 chars">
        </div>
        <div class="flex items-end gap-3">
            <button class="bg-deep-royal text-pure-white px-6 py-2 rounded-lg font-label-caps hover:brightness-110 transition-all">Save</button>
            <button type="button" class="px-4 py-2 rounded-lg border border-divider-gray text-sm hover:bg-surface-container transition-all" onclick="toggleForm()">Cancel</button>
        </div>
    </form>
</div>

<div class="glass-card rounded-xl overflow-hidden border border-on-surface/5">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-low border-b border-on-surface/10">
                <th class="px-6 py-4 font-label-caps text-on-surface-variant">User</th>
                <th class="px-6 py-4 font-label-caps text-on-surface-variant">Role</th>
                <th class="px-6 py-4 font-label-caps text-on-surface-variant">Last Login</th>
                <th class="px-6 py-4 font-label-caps text-on-surface-variant">Created</th>
                <th class="px-6 py-4 font-label-caps text-on-surface-variant text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-on-surface/5">
            <?php foreach ($users as $u): ?>
            <tr class="hover:bg-surface-bright transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-deep-royal flex items-center justify-center text-pure-white font-bold text-sm"><?= strtoupper(substr($u['display_name'] ?? $u['username'], 0, 2)) ?></div>
                        <div>
                            <p class="font-bold text-deep-royal"><?= Security::h($u['display_name'] ?: $u['username']) ?></p>
                            <p class="text-xs text-on-surface-variant"><?= Security::h($u['email']) ?></p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-full text-xs font-bold <?= $u['role'] === 'admin' ? 'bg-deep-royal/10 text-deep-royal' : ($u['role'] === 'editor' ? 'bg-vibrant-amber/20 text-amber-900' : 'bg-surface-container text-on-surface-variant') ?>">
                        <?= ucfirst($u['role']) ?>
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-on-surface-variant"><?= $u['last_login'] ? formatDate($u['last_login'], 'M d, Y H:i') : 'Never' ?></td>
                <td class="px-6 py-4 text-sm text-on-surface-variant"><?= formatDate($u['created_at'], 'M d, Y') ?></td>
                <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                        <button class="p-2 hover:bg-surface-container rounded-lg text-on-surface-variant transition-colors" onclick="editUser(<?= $u['id'] ?>, '<?= Security::h($u['username']) ?>', '<?= Security::h($u['email']) ?>', '<?= Security::h($u['display_name'] ?: '') ?>', '<?= $u['role'] ?>')">
                            <span class="material-symbols-outlined">edit</span>
                        </button>
                        <?php if ($u['id'] !== Auth::id()): ?>
                        <form method="POST" class="inline">
                            <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                            <button class="p-2 hover:bg-error/10 rounded-lg transition-colors" style="color: #ba1a1a;" data-confirm="Delete user <?= Security::h($u['username']) ?>?" data-confirm-title="Delete User" data-confirm-ok="Delete"><span class="material-symbols-outlined">delete</span></button>
                        </form>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function toggleForm() {
    var form = document.getElementById('userForm');
    form.classList.toggle('hidden');
}

function editUser(id, username, email, displayName, role) {
    document.getElementById('userFormAction').value = 'update';
    document.getElementById('userId').value = id;
    document.getElementById('inputUsername').value = username;
    document.getElementById('inputEmail').value = email;
    document.getElementById('inputDisplayName').value = displayName;
    document.getElementById('inputRole').value = role;
    document.getElementById('inputPassword').placeholder = 'Leave blank to keep current';
    document.getElementById('userForm').classList.remove('hidden');
}
</script>

<?php require_once BASE_PATH . 'includes/admin-footer.php'; ?>
