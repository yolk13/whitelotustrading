<?php
Auth::require();
$currentUser = Auth::user();
$pageTitle = $pageTitle ?? 'Admin Panel';
$currentRoute = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
function isActiveNav(string $path): bool {
    global $currentRoute;
    if ($path === '/admin') {
        return $currentRoute === '/admin' || $currentRoute === '/';
    }
    return $currentRoute !== null && str_starts_with($currentRoute, $path);
}
function navClass(string $path): string {
    return isActiveNav($path) ? 'bg-primary-container text-on-primary-container rounded-lg font-bold translate-x-1' : 'text-on-surface-variant hover:bg-surface-container-high rounded-lg';
}
function navFill(string $path): string {
    return isActiveNav($path) ? ' style="font-variation-settings: \'FILL\' 1;"' : '';
}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= Security::h($pageTitle) ?> | White Lotus Trading</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect width=%22100%22 height=%22100%22 rx=%2220%22 fill=%22%23002366%22/><text x=%2250%22 y=%2268%22 text-anchor=%22middle%22 font-family=%22sans-serif%22 font-size=%2242%22 font-weight=%22800%22 fill=%22%23FFBF00%22>WL</text></svg>">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
<script>
tailwind.config = {
    darkMode: "class",
    theme: {
        extend: {
            colors: {
                "surface-container-low": "#f6f3f2",
                "deep-royal": "#002366",
                "on-secondary": "#ffffff",
                "error": "#ba1a1a",
                "surface-container-highest": "#e5e2e1",
                "surface-variant": "#e5e2e1",
                "error-container": "#ffdad6",
                "surface-bright": "#fcf9f8",
                "primary-container": "#002366",
                "secondary-fixed": "#ffdfa0",
                "on-error-container": "#93000a",
                "surface-dim": "#dcd9d9",
                "tertiary-container": "#26292a",
                "on-error": "#ffffff",
                "primary": "#00113a",
                "on-background": "#1c1b1b",
                "inverse-on-surface": "#f3f0ef",
                "on-surface-variant": "#444650",
                "surface-container-lowest": "#ffffff",
                "on-tertiary": "#ffffff",
                "charcoal-text": "#1A1A1A",
                "on-primary-container": "#758dd5",
                "outline": "#757682",
                "surface-container": "#f0eded",
                "secondary": "#795900",
                "on-primary": "#ffffff",
                "secondary-container": "#ffbf00",
                "divider-gray": "#F8F9FA",
                "on-surface": "#1c1b1b",
                "surface": "#fcf9f8",
                "inverse-surface": "#313030",
                "surface-container-high": "#eae7e7",
                "vibrant-amber": "#FFBF00",
                "background": "#fcf9f8",
                "tertiary": "#121516",
                "pure-white": "#FFFFFF"
            },
            borderRadius: { "DEFAULT": "0.125rem", "lg": "0.25rem", "xl": "0.5rem", "full": "0.75rem" },
            spacing: { "gutter": "24px", "section-gap": "120px", "margin-mobile": "16px", "margin-desktop": "80px", "base-unit": "8px" },
            fontFamily: { "body-md": ["Inter"], "label-caps": ["Inter"], "headline-sm": ["Plus Jakarta Sans"], "headline-md": ["Plus Jakarta Sans"], "display-lg": ["Plus Jakarta Sans"] },
            fontSize: {
                "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                "label-caps": ["12px", {"lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "700"}],
                "headline-sm": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                "headline-md": ["30px", {"lineHeight": "38px", "fontWeight": "600"}],
                "display-lg": ["48px", {"lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "700"}]
            }
        }
    }
}
</script>
<style>
.glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(20px); border: 1px solid rgba(248, 249, 250, 0.1); }
.material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }
.hide-scrollbar::-webkit-scrollbar { display: none; }
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
.active-nav-shadow { box-shadow: 0px 4px 20px rgba(0, 35, 102, 0.05); }
</style>
</head>
<body class="bg-surface font-body-md text-on-surface min-h-screen">
<div class="flex min-h-screen">
<aside class="h-screen w-64 fixed left-0 top-0 z-50 bg-surface-container border-r border-on-surface/10 flex flex-col p-base-unit">
    <div class="px-4 py-6 mb-6">
        <a href="/admin" class="block">
            <h1 class="font-headline-sm text-headline-sm text-deep-royal leading-tight">WL Admin</h1>
            <p class="font-label-caps text-label-caps text-on-surface-variant opacity-70">Control Center</p>
        </a>
    </div>
    <nav class="flex-1 space-y-1 px-2">
        <a class="flex items-center gap-3 px-4 py-3 transition-all <?= $currentRoute === '/admin' || $currentRoute === '/' ? 'bg-primary-container text-pure-white font-bold active-nav-shadow' : navClass('/admin') ?>" href="/admin">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="font-label-caps text-label-caps">Dashboard</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 transition-all <?= navClass('/admin/products') ?>" href="/admin/products">
            <span class="material-symbols-outlined"<?= navFill('/admin/products') ?>>inventory_2</span>
            <span class="font-label-caps text-label-caps">Products</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 transition-all <?= navClass('/admin/inquiries') ?>" href="/admin/inquiries">
            <span class="material-symbols-outlined"<?= navFill('/admin/inquiries') ?>>mail</span>
            <span class="font-label-caps text-label-caps">Inquiries</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 transition-all <?= navClass('/admin/pages') ?>" href="/admin/pages">
            <span class="material-symbols-outlined"<?= navFill('/admin/pages') ?>>edit_note</span>
            <span class="font-label-caps text-label-caps">Pages</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 transition-all <?= navClass('/admin/blog') ?>" href="/admin/blog">
            <span class="material-symbols-outlined"<?= navFill('/admin/blog') ?>>article</span>
            <span class="font-label-caps text-label-caps">Blog</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 transition-all <?= navClass('/admin/media') ?>" href="/admin/media">
            <span class="material-symbols-outlined"<?= navFill('/admin/media') ?>>photo_library</span>
            <span class="font-label-caps text-label-caps">Media</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 transition-all <?= navClass('/admin/subscribers') ?>" href="/admin/subscribers">
            <span class="material-symbols-outlined"<?= navFill('/admin/subscribers') ?>>mail_list</span>
            <span class="font-label-caps text-label-caps">Subscribers</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 transition-all <?= navClass('/admin/categories') ?>" href="/admin/categories">
            <span class="material-symbols-outlined"<?= navFill('/admin/categories') ?>>category</span>
            <span class="font-label-caps text-label-caps">Categories</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 transition-all <?= navClass('/admin/settings') ?>" href="/admin/settings">
            <span class="material-symbols-outlined"<?= navFill('/admin/settings') ?>>settings</span>
            <span class="font-label-caps text-label-caps">Home Settings</span>
        </a>
        <form method="POST" action="/admin/logout" class="block">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:bg-surface-container-high transition-all rounded-lg group cursor-pointer">
                <span class="material-symbols-outlined">logout</span>
                <span class="font-label-caps text-label-caps">Logout</span>
            </button>
        </form>
    </nav>
    <div class="px-4 py-4 mt-auto border-t border-on-surface/10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-deep-royal flex items-center justify-center text-pure-white font-bold text-sm"><?= strtoupper(substr($currentUser['display_name'] ?? $currentUser['username'], 0, 2)) ?></div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-deep-royal truncate"><?= Security::h($currentUser['display_name'] ?? $currentUser['username']) ?></p>
                <p class="text-xs text-on-surface-variant capitalize"><?= Security::h($currentUser['role'] ?? 'admin') ?></p>
            </div>
        </div>
    </div>
</aside>
<main class="flex-1 ml-64 bg-surface-bright min-h-screen pb-section-gap">
<header class="sticky top-0 w-full z-40 bg-pure-white/80 backdrop-blur-md border-b border-on-surface/10 px-margin-desktop py-4 flex justify-between items-center">
    <h2 class="font-headline-sm text-headline-sm text-deep-royal"><?= Security::h($pageTitle) ?></h2>
    <div class="flex items-center gap-4">
        <a href="/" target="_blank" class="text-on-surface-variant hover:text-deep-royal transition-colors">
            <span class="material-symbols-outlined">open_in_new</span>
        </a>
    </div>
</header>
<div class="px-margin-desktop mt-gutter space-y-gutter">
