<?php

$pageTitle = 'Dashboard';
$pagesCount = Page::count("status = 'published'");
$productsCount = Product::count("status = 'active'");
$inquiriesCount = Inquiry::countUnread();
$recentInquiries = Inquiry::recent(5);

require_once BASE_PATH . 'includes/admin-header.php';
?>

<div class="relative w-full h-48 rounded-2xl overflow-hidden glass-card flex items-center p-10 border border-on-surface/10" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); border: 1px solid rgba(26,26,26,0.1);">
    <div class="relative z-10 max-w-lg">
        <h3 class="font-display-lg text-display-lg text-deep-royal mb-2" style="font-size: 30px; font-weight: 700;">Systems Nominal.</h3>
        <p class="text-body-lg text-on-surface-variant" style="font-family: Inter; font-size: 18px;">White Lotus Trading is operating at peak efficiency across all divisions.</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">
    <div class="glass-card p-8 rounded-2xl border border-on-surface/5 flex flex-col gap-4 group hover:border-deep-royal/20 transition-all" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(20px);">
        <div class="flex justify-between items-start">
            <div class="p-3 bg-primary-fixed text-deep-royal rounded-xl" style="background-color: #dbe1ff;">
                <span class="material-symbols-outlined">description</span>
            </div>
            <span class="text-secondary font-bold font-label-caps" style="color: #795900;">Published</span>
        </div>
        <div>
            <p class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Pages Published</p>
            <p class="font-display-lg text-[40px] text-deep-royal"><?= $pagesCount ?></p>
        </div>
    </div>
    <div class="glass-card p-8 rounded-2xl border border-on-surface/5 flex flex-col gap-4 group hover:border-vibrant-amber/20 transition-all" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(20px);">
        <div class="flex justify-between items-start">
            <div class="p-3 rounded-xl" style="background-color: #ffdfa0; color: #795900;">
                <span class="material-symbols-outlined">inventory_2</span>
            </div>
            <span class="text-on-surface-variant font-bold font-label-caps">Active</span>
        </div>
        <div>
            <p class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Active Products</p>
            <p class="font-display-lg text-[40px] text-deep-royal"><?= $productsCount ?></p>
        </div>
    </div>
    <div class="glass-card p-8 rounded-2xl border border-on-surface/5 flex flex-col gap-4 group hover:border-error/20 transition-all" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(20px);">
        <div class="flex justify-between items-start">
            <div class="p-3 rounded-xl" style="background-color: #ffdad6; color: #ba1a1a;">
                <span class="material-symbols-outlined">mark_email_unread</span>
            </div>
            <span class="text-error font-bold font-label-caps" style="color: #ba1a1a;"><?= $inquiriesCount > 0 ? 'Urgent' : 'Clear' ?></span>
        </div>
        <div>
            <p class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Unread Inquiries</p>
            <p class="font-display-lg text-[40px] text-deep-royal"><?= $inquiriesCount ?></p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
    <div class="lg:col-span-4 space-y-4">
        <h4 class="font-headline-sm text-headline-sm text-deep-royal px-1">Quick Actions</h4>
        <a href="/admin/products" class="block w-full glass-card p-6 rounded-xl flex items-center justify-between hover:bg-surface-container-high transition-all group" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(20px);">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-surface flex items-center justify-center text-deep-royal shadow-sm" style="background: #fcf9f8;">
                    <span class="material-symbols-outlined">add_box</span>
                </div>
                <span class="font-body-md font-bold text-deep-royal">Add New Product</span>
            </div>
            <span class="material-symbols-outlined text-on-surface-variant group-hover:translate-x-1 transition-transform">chevron_right</span>
        </a>
        <a href="/admin/pages" class="block w-full glass-card p-6 rounded-xl flex items-center justify-between hover:bg-surface-container-high transition-all group" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(20px);">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-surface flex items-center justify-center text-deep-royal shadow-sm" style="background: #fcf9f8;">
                    <span class="material-symbols-outlined">dashboard_customize</span>
                </div>
                <span class="font-body-md font-bold text-deep-royal">Edit Page Content</span>
            </div>
            <span class="material-symbols-outlined text-on-surface-variant group-hover:translate-x-1 transition-transform">chevron_right</span>
        </a>
        <a href="/admin/inquiries" class="block w-full glass-card p-6 rounded-xl flex items-center justify-between hover:bg-surface-container-high transition-all group" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(20px);">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-surface flex items-center justify-center text-deep-royal shadow-sm" style="background: #fcf9f8;">
                    <span class="material-symbols-outlined">chat_bubble</span>
                </div>
                <span class="font-body-md font-bold text-deep-royal">View Customer Messages</span>
            </div>
            <div class="flex items-center gap-2">
                <?php if ($inquiriesCount > 0): ?>
                    <span class="w-2 h-2 rounded-full bg-vibrant-amber animate-pulse"></span>
                <?php endif; ?>
                <span class="material-symbols-outlined text-on-surface-variant group-hover:translate-x-1 transition-transform">chevron_right</span>
            </div>
        </a>
    </div>
    <div class="lg:col-span-8">
        <div class="glass-card rounded-2xl border border-on-surface/5 overflow-hidden" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(20px);">
            <div class="px-8 py-6 border-b border-on-surface/5 flex justify-between items-center">
                <h4 class="font-headline-sm text-headline-sm text-deep-royal">Recent Inquiries</h4>
                <a class="font-label-caps text-label-caps text-on-primary-container hover:underline" href="/admin/inquiries">View All</a>
            </div>
            <?php if (empty($recentInquiries)): ?>
                <div class="px-8 py-12 text-center text-on-surface-variant font-body-md">No inquiries yet.</div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-surface-container-low" style="background-color: #f6f3f2;">
                        <tr>
                            <th class="px-8 py-4 font-label-caps text-label-caps text-on-surface-variant">CLIENT</th>
                            <th class="px-8 py-4 font-label-caps text-label-caps text-on-surface-variant">DIVISION</th>
                            <th class="px-8 py-4 font-label-caps text-label-caps text-on-surface-variant">STATUS</th>
                            <th class="px-8 py-4 font-label-caps text-label-caps text-on-surface-variant">DATE</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-on-surface/5">
                        <?php foreach ($recentInquiries as $inq): ?>
                        <tr class="hover:bg-surface-container-high transition-colors">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-deep-royal flex items-center justify-center text-[10px] text-pure-white font-bold"><?= strtoupper(substr($inq['name'], 0, 2)) ?></div>
                                    <div>
                                        <p class="font-bold text-deep-royal"><?= Security::h($inq['name']) ?></p>
                                        <p class="text-xs text-on-surface-variant"><?= Security::h($inq['email']) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <span class="px-3 py-1 rounded-full bg-surface-container-highest text-charcoal-text text-[11px] font-bold"><?= Security::h($inq['division'] ?: 'General') ?></span>
                            </td>
                            <td class="px-8 py-5">
                                <span class="flex items-center gap-2 text-xs font-bold" style="color: <?= $inq['status'] === 'new' ? '#FFBF00' : ($inq['status'] === 'replied' ? '#002366' : '#444650') ?>;">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background-color: <?= $inq['status'] === 'new' ? '#FFBF00' : ($inq['status'] === 'replied' ? '#002366' : '#444650') ?>;"></span>
                                    <?= ucfirst(Security::h($inq['status'])) ?>
                                </span>
                            </td>
                            <td class="px-8 py-5 text-xs text-on-surface-variant"><?= formatDate($inq['created_at'], 'M d, Y') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . 'includes/admin-footer.php'; ?>
