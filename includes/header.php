<!DOCTYPE html>
<html class="scroll-smooth" lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= Security::h($pageTitle ?? 'White Lotus Trading - F.Z.E. | HVAC & Wellness') ?></title>
<link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect width=%22100%22 height=%22100%22 rx=%2220%22 fill=%22%23002366%22/><text x=%2250%22 y=%2268%22 text-anchor=%22middle%22 font-family=%22sans-serif%22 font-size=%2242%22 font-weight=%22800%22 fill=%22%23FFBF00%22>WL</text></svg>">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script>
tailwind.config = {
    darkMode: "class",
    theme: {
        extend: {
            colors: {
                "surface-container-low": "#f6f3f2", "deep-royal": "#002366", "on-secondary": "#ffffff",
                "error": "#ba1a1a", "surface-container-highest": "#e5e2e1", "surface-variant": "#e5e2e1",
                "error-container": "#ffdad6", "surface-bright": "#fcf9f8", "primary-container": "#002366",
                "secondary-fixed": "#ffdfa0", "on-error-container": "#93000a", "surface-dim": "#dcd9d9",
                "tertiary-container": "#26292a", "on-error": "#ffffff", "primary": "#00113a",
                "on-background": "#1c1b1b", "primary-fixed-dim": "#b3c5ff", "inverse-on-surface": "#f3f0ef",
                "surface-container-lowest": "#ffffff", "secondary-fixed-dim": "#fbbc00", "on-tertiary": "#ffffff",
                "charcoal-text": "#1A1A1A", "on-primary-container": "#758dd5", "outline": "#757682",
                "surface-container": "#f0eded", "secondary": "#795900", "on-primary": "#ffffff",
                "divider-gray": "#F8F9FA", "on-surface": "#1c1b1b", "surface": "#fcf9f8",
                "inverse-surface": "#313030", "surface-container-high": "#eae7e7", "inverse-primary": "#b3c5ff",
                "vibrant-amber": "#FFBF00", "background": "#fcf9f8", "pure-white": "#FFFFFF",
            },
            borderRadius: { DEFAULT: "0.125rem", lg: "0.25rem", xl: "0.5rem", full: "0.75rem" },
            spacing: { gutter: "24px", "section-gap": "120px", "margin-mobile": "16px", "margin-desktop": "80px", "base-unit": "8px" },
            fontFamily: { "body-md": ["Inter"], "label-caps": ["Inter"], "headline-sm": ["Plus Jakarta Sans"], "headline-md": ["Plus Jakarta Sans"], "display-lg": ["Plus Jakarta Sans"], "display-lg-mobile": ["Plus Jakarta Sans"], "body-lg": ["Inter"] },
            fontSize: {
                "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                "label-caps": ["12px", {"lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "700"}],
                "headline-sm": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                "headline-md": ["30px", {"lineHeight": "38px", "fontWeight": "600"}],
                "display-lg": ["48px", {"lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                "display-lg-mobile": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "700"}],
                "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}]
            }
        }
    }
}
</script>
<style>
.glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(20px); border: 1px solid rgba(26, 26, 26, 0.1); }
.hero-split { height: calc(100vh - 80px); min-height: 600px; }
.tab-active { border-bottom: 2px solid #FFBF00; color: #002366; }
.material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fadeIn 0.6s ease-out forwards; }
</style>
</head>
<body class="bg-pure-white text-on-surface font-body-md overflow-x-hidden">

<header class="sticky top-0 w-full z-50 bg-pure-white/80 backdrop-blur-md border-b border-on-surface/10 shadow-sm">
    <div class="max-w-[1280px] mx-auto px-margin-mobile md:px-margin-desktop py-4 flex justify-between items-center">
        <a href="/" class="flex items-center gap-4">
            <div class="h-10 w-10 rounded-full bg-deep-royal flex items-center justify-center text-pure-white font-bold text-sm">WL</div>
            <span class="font-headline-md text-headline-md font-extrabold text-deep-royal tracking-tight">White Lotus Trading</span>
        </a>
        <nav class="hidden md:flex items-center space-x-8">
            <a class="font-label-caps text-label-caps text-on-surface-variant font-medium hover:text-vibrant-amber transition-colors <?= isActive('/') ? 'text-deep-royal border-b-2 border-vibrant-amber pb-1' : '' ?>" href="/">Home</a>
            <a class="font-label-caps text-label-caps text-on-surface-variant font-medium hover:text-vibrant-amber transition-colors <?= isActive('/products') ? 'text-deep-royal border-b-2 border-vibrant-amber pb-1' : '' ?>" href="/products">Products</a>
            <a class="font-label-caps text-label-caps text-on-surface-variant font-medium hover:text-vibrant-amber transition-colors <?= isActive('/blog') ? 'text-deep-royal border-b-2 border-vibrant-amber pb-1' : '' ?>" href="/blog">Blog</a>
            <a class="font-label-caps text-label-caps text-on-surface-variant font-medium hover:text-vibrant-amber transition-colors <?= isActive('/inquiry') ? 'text-deep-royal border-b-2 border-vibrant-amber pb-1' : '' ?>" href="/inquiry">Inquiry</a>
            <a class="font-label-caps text-label-caps text-on-surface-variant font-medium hover:text-vibrant-amber transition-colors <?= isActive('/contact') ? 'text-deep-royal border-b-2 border-vibrant-amber pb-1' : '' ?>" href="/contact">Contact</a>
            <a href="/search" class="text-on-surface-variant hover:text-deep-royal transition-colors">
                <span class="material-symbols-outlined">search</span>
            </a>
            <a href="/inquiry" class="bg-deep-royal text-pure-white px-6 py-2 rounded-lg font-label-caps text-label-caps hover:bg-vibrant-amber hover:text-deep-royal transition-all active:scale-95">Get a Quote</a>
        </nav>
        <button class="md:hidden text-deep-royal" onclick="document.getElementById('mobileNav').classList.toggle('hidden')">
            <span class="material-symbols-outlined">menu</span>
        </button>
    </div>
    <div id="mobileNav" class="hidden md:hidden bg-pure-white border-t border-on-surface/10 px-margin-mobile py-4 space-y-4">
        <a class="block font-label-caps text-label-caps text-deep-royal" href="/">Home</a>
        <a class="block font-label-caps text-label-caps text-on-surface-variant" href="/products">Products</a>
        <a class="block font-label-caps text-label-caps text-on-surface-variant" href="/blog">Blog</a>
        <a class="block font-label-caps text-label-caps text-on-surface-variant" href="/inquiry">Inquiry</a>
        <a class="block font-label-caps text-label-caps text-on-surface-variant" href="/contact">Contact</a>
        <a href="/inquiry" class="block bg-deep-royal text-pure-white px-6 py-2 rounded-lg font-label-caps text-center">Get a Quote</a>
    </div>
</header>
<main>
