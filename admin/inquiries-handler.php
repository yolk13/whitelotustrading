<?php

$pageTitle = 'Inquiries Management';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
        $message = 'Invalid security token. Please try again.';
    } else {
    $action = $_POST['action'] ?? '';

    if ($action === 'reply') {
        $id = (int)($_POST['id'] ?? 0);
        $reply = Security::sanitizeRich($_POST['reply_message'] ?? '');
        if ($id && $reply) {
            Database::insert('inquiry_replies', [
                'inquiry_id' => $id,
                'admin_user_id' => Auth::id(),
                'message' => $reply,
            ]);
            Database::query("UPDATE inquiries SET status = 'replied' WHERE id = ?", [$id]);
            $inquiry = Inquiry::find($id);
            if ($inquiry) {
                Mail::sendInquiryReply($inquiry, $reply);
            }
            Audit::log('reply', 'inquiry', $id, json_encode(['message_preview' => mb_substr($reply, 0, 100)]));
            $message = 'Reply sent.';
        }
    }

    if ($action === 'status') {
        $id = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        if ($id && in_array($status, ['new', 'replied', 'closed'])) {
            Inquiry::markStatus($id, $status);
            $message = 'Status updated.';
        }
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            Inquiry::delete($id);
            $message = 'Inquiry deleted.';
        }
    }
    }
}

$page = max(1, (int)($_GET['page'] ?? 1));
$filter = Security::sanitize($_GET['filter'] ?? 'all');
$search = Security::sanitize($_GET['search'] ?? '');

$inquiries = Inquiry::paginateFiltered($page, 15, $filter, $search);

require_once BASE_PATH . 'includes/admin-header.php';
?>

<?php if ($message): ?>
    <div class="bg-primary-fixed text-deep-royal px-6 py-3 rounded-lg font-body-md mb-4" style="background-color: #dbe1ff;"><?= Security::h($message) ?></div>
<?php endif; ?>

<div class="flex h-[calc(100vh-200px)] -mx-margin-desktop -mt-gutter">
    <div class="w-full lg:w-[450px] border-r border-on-surface/10 overflow-y-auto bg-pure-white transition-all duration-300" style="scrollbar-width: thin;">
        <div class="sticky top-0 bg-pure-white/95 backdrop-blur z-10 p-4 border-b border-on-surface/5 flex gap-2">
            <a href="?filter=all" class="px-3 py-1.5 rounded-full font-label-caps text-[11px] uppercase transition-colors <?= $filter === 'all' ? 'bg-deep-royal text-pure-white' : 'hover:bg-surface-container' ?>">All</a>
            <a href="?filter=unread" class="px-3 py-1.5 rounded-full font-label-caps text-[11px] uppercase transition-colors <?= $filter === 'unread' ? 'bg-deep-royal text-pure-white' : 'hover:bg-surface-container' ?>">Unread</a>
            <a href="?filter=flagged" class="px-3 py-1.5 rounded-full font-label-caps text-[11px] uppercase transition-colors <?= $filter === 'flagged' ? 'bg-deep-royal text-pure-white' : 'hover:bg-surface-container' ?>">Flagged</a>
            <div class="ml-auto relative">
                <input class="w-32 pl-3 pr-2 py-1 bg-surface-container-low rounded-full text-xs focus:outline-none focus:ring-1 focus:ring-deep-royal" name="search" placeholder="Search..." value="<?= Security::h($search) ?>" onchange="window.location='?filter=<?= $filter ?>&search='+encodeURIComponent(this.value)">
            </div>
        </div>

        <?php if (empty($inquiries['items'])): ?>
            <div class="p-8 text-center text-on-surface-variant font-body-md">No inquiries found.</div>
        <?php else: ?>
            <?php foreach ($inquiries['items'] as $inq): ?>
            <?php
            $inq['csrf_token'] = Security::generateCsrfToken();
            $inq['replies'] = Database::fetchAll(
                "SELECT r.*, u.display_name AS admin_name FROM inquiry_replies r LEFT JOIN users u ON r.admin_user_id = u.id WHERE r.inquiry_id = ? ORDER BY r.created_at ASC",
                [$inq['id']]
            );
            if (!empty($inq['customer_id'])) {
                $inq['previous_inquiries'] = Database::fetchAll(
                    "SELECT id, subject, message, status, created_at FROM inquiries WHERE customer_id = ? AND id != ? ORDER BY created_at DESC LIMIT 5",
                    [$inq['customer_id'], $inq['id']]
                );
            } else {
                $inq['previous_inquiries'] = [];
            }
            ?>
            <div class="border-b border-on-surface/5 p-5 cursor-pointer hover:bg-surface-container-low transition-all group <?= !$inq['is_read'] ? 'bg-primary-container/5' : '' ?>" onclick="openInquiry(<?= $inq['id'] ?>, <?= Security::h(json_encode($inq)) ?>)">
                <div class="flex justify-between items-start mb-1">
                    <span class="font-headline-sm text-body-md font-bold text-deep-royal"><?= Security::h($inq['name']) ?></span>
                    <span class="text-[11px] font-label-caps text-on-surface-variant"><?= formatDate($inq['created_at'], 'M d') ?></span>
                </div>
                <p class="font-bold text-on-surface mb-1 truncate"><?= Security::h($inq['subject']) ?></p>
                <p class="text-body-md text-on-surface-variant line-clamp-2 text-sm"><?= Security::h($inq['message']) ?></p>
                <div class="flex items-center mt-3 gap-3">
                    <?php if ($inq['division']): ?>
                        <span class="text-[10px] font-label-caps text-on-surface-variant/60 uppercase"><?= Security::h($inq['division']) ?></span>
                    <?php endif; ?>
                    <?php if ($inq['status'] === 'new'): ?>
                        <span class="text-[10px] font-label-caps text-vibrant-amber uppercase font-extrabold flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">priority_high</span> New
                        </span>
                    <?php elseif ($inq['status'] === 'replied'): ?>
                        <span class="text-[10px] font-label-caps text-deep-royal uppercase">Replied</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="hidden lg:flex flex-1 flex-col bg-surface-bright overflow-hidden" id="detail-view">
        <div class="flex-1 flex flex-col items-center justify-center p-12 text-center" id="empty-state">
            <div class="w-32 h-32 rounded-full bg-surface-container flex items-center justify-center mb-6">
                <span class="material-symbols-outlined text-deep-royal/20 text-[64px]" style="font-variation-settings: 'wght' 200;">drafts</span>
            </div>
            <h3 class="font-headline-md text-headline-md text-deep-royal mb-2">Select an inquiry</h3>
            <p class="text-body-md text-on-surface-variant max-w-xs">Select an inquiry from the list to view the full message details and respond.</p>
        </div>
    </div>
</div>

<div class="fixed inset-0 z-[60] hidden" id="inquiryModal">
    <div class="absolute inset-0 bg-deep-royal/20 backdrop-blur-sm" onclick="toggleModal('inquiryModal')"></div>
    <div class="absolute right-0 top-0 h-full w-full max-w-2xl bg-pure-white shadow-2xl flex flex-col">
        <div class="px-8 py-6 border-b border-on-surface/10 flex items-center justify-between">
            <h3 class="font-headline-sm text-headline-sm text-deep-royal" id="detail-subject">Subject</h3>
            <div class="flex items-center gap-2">
                <button class="p-2 hover:bg-surface-container rounded-lg material-symbols-outlined transition-colors" onclick="toggleModal('inquiryModal')">close</button>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto p-8">
            <div class="max-w-3xl mx-auto space-y-8">
                <div class="glass-card p-6 rounded-xl flex items-start gap-4" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(20px);">
                    <div class="w-12 h-12 rounded bg-deep-royal/10 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-deep-royal">person</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between">
                            <h4 class="font-bold text-deep-royal" id="detail-name"></h4>
                            <span class="text-[11px] font-label-caps text-on-surface-variant" id="detail-date"></span>
                        </div>
                        <p class="text-on-surface-variant text-[13px] mt-1" id="detail-email"></p>
                        <p class="text-on-surface-variant text-[13px]" id="detail-phone"></p>
                        <p class="text-on-surface-variant text-[13px]" id="detail-company"></p>
                    </div>
                </div>
                <div class="space-y-4" id="detail-message"></div>
            </div>
        </div>
        <div class="p-6 bg-pure-white border-t border-on-surface/5">
            <form method="POST" class="max-w-3xl mx-auto">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
                <input type="hidden" name="action" value="reply">
                <input type="hidden" name="id" id="replyId" value="">
                <div class="relative">
                    <textarea class="w-full h-32 px-4 py-4 bg-surface-container-low border-transparent focus:border-deep-royal focus:ring-0 rounded-xl text-body-md transition-all resize-none" name="reply_message" placeholder="Type your reply here..."></textarea>
                    <div class="absolute bottom-4 right-4 flex items-center gap-3">
                        <button type="submit" class="bg-vibrant-amber text-charcoal-text px-6 py-2 rounded font-bold font-label-caps text-[11px] uppercase shadow-sm hover:translate-y-[-1px] transition-all">
                            Send Reply
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openInquiry(id, inq) {
    if (!inq.is_read) {
        fetch('/admin/inquiries', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'csrf_token=' + encodeURIComponent(inq.csrf_token) + '&action=status&id=' + id + '&status=new'
        });
    }
    document.getElementById('detail-subject').textContent = inq.subject;
    document.getElementById('detail-name').textContent = inq.name;
    document.getElementById('detail-email').textContent = 'Email: ' + inq.email;
    document.getElementById('detail-phone').textContent = inq.phone ? 'Phone: ' + inq.phone : '';
    document.getElementById('detail-company').textContent = inq.company ? 'Company: ' + inq.company : '';
    document.getElementById('detail-date').textContent = inq.created_at;
    var msgHtml = '<div class="p-6 bg-surface-container-low rounded-xl"><p class="text-body-md text-on-surface leading-relaxed">' + inq.message.replace(/\n/g, '<br>') + '</p></div>';

    if (inq.replies && inq.replies.length > 0) {
        msgHtml += '<div class="mt-6"><h4 class="font-label-caps text-on-surface-variant mb-3">Replies</h4>';
        inq.replies.forEach(function(r) {
            msgHtml += '<div class="p-4 bg-deep-royal/5 rounded-xl mb-2 border-l-4 border-deep-royal"><p class="text-sm text-on-surface">' + r.message.replace(/\n/g, '<br>') + '</p><p class="text-[10px] text-on-surface-variant mt-2">' + r.created_at + ' by ' + r.admin_name + '</p></div>';
        });
        msgHtml += '</div>';
    }

    if (inq.previous_inquiries && inq.previous_inquiries.length > 0) {
        msgHtml += '<div class="mt-6"><h4 class="font-label-caps text-on-surface-variant mb-3">Previous Inquiries</h4>';
        inq.previous_inquiries.forEach(function(pi) {
            msgHtml += '<a href="#" onclick="openInquiry(' + pi.id + ', ' + JSON.stringify(pi) + '); return false;" class="block p-3 bg-surface-container-low rounded-xl mb-2 hover:bg-surface-container-high transition-colors"><p class="text-sm font-bold text-deep-royal">' + pi.subject + '</p><p class="text-[10px] text-on-surface-variant">' + pi.created_at + ' — ' + pi.status + '</p></a>';
        });
        msgHtml += '</div>';
    }

    document.getElementById('detail-message').innerHTML = msgHtml;
    document.getElementById('replyId').value = id;
    toggleModal('inquiryModal');
}

function toggleModal(id) {
    const modal = document.getElementById(id);
    modal.classList.toggle('hidden');
}
</script>

<?php require_once BASE_PATH . 'includes/admin-footer.php'; ?>
