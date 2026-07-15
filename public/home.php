<?php

$pageTitle = 'White Lotus Trading - F.Z.E. | HVAC & Wellness';
$activeProducts = Product::active();
$hvacProducts = array_filter($activeProducts, fn($p) => $p['division'] === 'HVAC');
$wellnessProducts = array_filter($activeProducts, fn($p) => $p['division'] === 'Consumables');

require_once BASE_PATH . 'includes/header.php';
?>

<section class="relative hero-split w-full flex flex-col md:flex-row overflow-hidden">
    <div class="w-full md:w-1/2 flex items-center justify-center bg-pure-white z-10 px-margin-mobile md:px-margin-desktop">
        <div class="max-w-xl space-y-8 animate-fade-in">
            <span class="font-label-caps text-label-caps text-vibrant-amber uppercase tracking-widest block mb-4">Established Excellence</span>
            <h1 class="font-display-lg text-display-lg-mobile md:text-display-lg text-deep-royal leading-tight">
                Your Trusted Partner in <span class="text-vibrant-amber">HVAC & Wellness</span>
            </h1>
            <p class="font-body-lg text-body-lg text-on-surface-variant max-w-md">
                Bridging industrial precision with organic vitality. We provide state-of-the-art climate solutions and premium health consumables for a balanced lifestyle.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 pt-4">
                <a href="/products" class="bg-vibrant-amber text-charcoal-text px-8 py-4 rounded-xl font-headline-sm text-headline-sm hover:shadow-lg transition-all flex items-center justify-center gap-2">
                    Explore Industrial
                    <span class="material-symbols-outlined">engineering</span>
                </a>
                <a href="/products?division=Consumables" class="border-2 border-deep-royal text-deep-royal px-8 py-4 rounded-xl font-headline-sm text-headline-sm hover:bg-deep-royal hover:text-pure-white transition-all flex items-center justify-center gap-2">
                    Wellness Shop
                    <span class="material-symbols-outlined">spa</span>
                </a>
            </div>
        </div>
    </div>
    <div class="w-full md:w-1/2 relative min-h-[400px] md:min-h-full bg-surface-container">
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="text-center text-on-surface-variant opacity-30">
                <span class="material-symbols-outlined text-[120px]">image</span>
                <p class="font-label-caps">Hero Image</p>
            </div>
        </div>
        <div class="absolute inset-0 bg-deep-royal/10"></div>
    </div>
</section>

<section class="py-section-gap px-margin-mobile md:px-margin-desktop max-w-[1280px] mx-auto">
    <div class="text-center mb-16">
        <h2 class="font-headline-md text-headline-md text-deep-royal mb-4">Our Core Divisions</h2>
        <div class="w-20 h-1 bg-vibrant-amber mx-auto"></div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-gutter">
        <div class="group relative glass-card p-12 rounded-2xl overflow-hidden hover:shadow-2xl transition-all duration-500 cursor-pointer">
            <div class="relative z-10 space-y-6">
                <div class="w-16 h-16 bg-deep-royal/10 rounded-full flex items-center justify-center text-deep-royal group-hover:bg-deep-royal group-hover:text-pure-white transition-colors duration-500">
                    <span class="material-symbols-outlined text-4xl">ac_unit</span>
                </div>
                <h3 class="font-headline-sm text-headline-sm text-deep-royal">Industrial HVAC Solutions</h3>
                <p class="font-body-md text-body-md text-on-surface-variant">Precision-engineered components, ventilation systems, and climate control technology for large-scale infrastructure and industrial applications.</p>
                <ul class="space-y-3 font-label-caps text-label-caps text-deep-royal/70">
                    <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm">check_circle</span> Precision Air Handling</li>
                    <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm">check_circle</span> Energy Efficient Cooling</li>
                    <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm">check_circle</span> Technical Maintenance</li>
                </ul>
            </div>
            <div class="absolute -right-12 -bottom-12 opacity-5 group-hover:opacity-10 transition-opacity">
                <span class="material-symbols-outlined text-[200px]">settings_suggest</span>
            </div>
        </div>
        <div class="group relative glass-card p-12 rounded-2xl overflow-hidden hover:shadow-2xl transition-all duration-500 cursor-pointer">
            <div class="relative z-10 space-y-6">
                <div class="w-16 h-16 bg-vibrant-amber/10 rounded-full flex items-center justify-center text-vibrant-amber group-hover:bg-vibrant-amber group-hover:text-charcoal-text transition-colors duration-500">
                    <span class="material-symbols-outlined text-4xl">potted_plant</span>
                </div>
                <h3 class="font-headline-sm text-headline-sm text-deep-royal">Organic Wellness Trading</h3>
                <p class="font-body-md text-body-md text-on-surface-variant">Sourcing the purest superfoods and traditional health supplements, including Himalayan Shilajit, high-grade spices, and natural wellness products.</p>
                <ul class="space-y-3 font-label-caps text-label-caps text-deep-royal/70">
                    <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm">check_circle</span> Ethically Sourced Shilajit</li>
                    <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm">check_circle</span> Premium Organic Spices</li>
                    <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm">check_circle</span> Direct Global Supply Chain</li>
                </ul>
            </div>
            <div class="absolute -right-12 -bottom-12 opacity-5 group-hover:opacity-10 transition-opacity">
                <span class="material-symbols-outlined text-[200px]">eco</span>
            </div>
        </div>
    </div>
</section>

<section class="py-section-gap bg-surface-container-low/50">
    <div class="max-w-[1280px] mx-auto px-margin-mobile md:px-margin-desktop">
        <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
            <div>
                <span class="font-label-caps text-label-caps text-deep-royal/60 mb-2 block uppercase">Product Catalog</span>
                <h2 class="font-headline-md text-headline-md text-deep-royal">Select Category</h2>
            </div>
            <div class="flex bg-pure-white p-1 rounded-xl shadow-sm">
                <button class="px-8 py-3 rounded-lg font-label-caps text-label-caps transition-all tab-active" id="btn-hvac" onclick="switchTab('hvac')">HVAC PRODUCTS</button>
                <button class="px-8 py-3 rounded-lg font-label-caps text-label-caps transition-all text-on-surface-variant hover:bg-surface-container" id="btn-wellness" onclick="switchTab('wellness')">CONSUMABLES</button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-gutter" id="grid-hvac">
            <?php if (empty($hvacProducts)): ?>
                <div class="col-span-full text-center py-12 text-on-surface-variant font-body-md">No HVAC products available yet.</div>
            <?php else: ?>
                <?php foreach (array_slice($hvacProducts, 0, 4) as $product): ?>
                <div class="group bg-pure-white p-4 rounded-2xl border border-divider-gray hover:shadow-xl transition-all">
                    <div class="aspect-square w-full rounded-xl overflow-hidden mb-6 bg-surface-container-low flex items-center justify-center relative">
                        <?php if ($product['image_url']): ?>
                            <img class="object-contain w-3/4 group-hover:scale-110 transition-transform duration-500" src="<?= uploadUrl($product['image_url']) ?>" alt="<?= Security::h($product['name']) ?>">
                        <?php else: ?>
                            <span class="material-symbols-outlined text-6xl text-on-surface-variant/30">inventory_2</span>
                        <?php endif; ?>
                        <span class="absolute top-4 left-4 bg-deep-royal text-pure-white text-[10px] font-bold px-2 py-1 rounded">INDUSTRIAL</span>
                    </div>
                    <h4 class="font-headline-sm text-body-lg text-deep-royal mb-1"><?= Security::h($product['name']) ?></h4>
                    <p class="font-label-caps text-[10px] text-on-surface-variant mb-4 tracking-wider uppercase"><?= Security::h($product['description'] ?: $product['sku']) ?></p>
                    <a href="/product/<?= Security::h($product['slug']) ?>" class="block w-full py-3 border border-deep-royal/20 text-deep-royal font-label-caps text-label-caps rounded-lg text-center hover:bg-deep-royal hover:text-pure-white transition-colors">View Specs</a>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-gutter" id="grid-wellness">
            <?php if (empty($wellnessProducts)): ?>
                <div class="col-span-full text-center py-12 text-on-surface-variant font-body-md">No wellness products available yet.</div>
            <?php else: ?>
                <?php foreach (array_slice($wellnessProducts, 0, 4) as $product): ?>
                <div class="group bg-pure-white p-4 rounded-2xl border border-divider-gray hover:shadow-xl transition-all">
                    <div class="aspect-square w-full rounded-xl overflow-hidden mb-6 bg-surface-container-low flex items-center justify-center relative">
                        <?php if ($product['image_url']): ?>
                            <img class="object-contain w-3/4 group-hover:scale-110 transition-transform duration-500" src="<?= uploadUrl($product['image_url']) ?>" alt="<?= Security::h($product['name']) ?>">
                        <?php else: ?>
                            <span class="material-symbols-outlined text-6xl text-on-surface-variant/30">spa</span>
                        <?php endif; ?>
                        <span class="absolute top-4 left-4 bg-vibrant-amber text-charcoal-text text-[10px] font-bold px-2 py-1 rounded">BEST SELLER</span>
                    </div>
                    <h4 class="font-headline-sm text-body-lg text-deep-royal mb-1"><?= Security::h($product['name']) ?></h4>
                    <p class="font-label-caps text-[10px] text-on-surface-variant mb-4 tracking-wider uppercase"><?= Security::h($product['description'] ?: $product['sku']) ?></p>
                    <a href="/product/<?= Security::h($product['slug']) ?>" class="block w-full py-3 border border-vibrant-amber/40 text-deep-royal font-label-caps text-label-caps rounded-lg text-center hover:bg-vibrant-amber hover:text-charcoal-text transition-colors">Shop Now</a>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="py-section-gap px-margin-mobile md:px-margin-desktop max-w-[1280px] mx-auto">
    <div class="flex flex-col lg:flex-row items-center gap-16">
        <div class="w-full lg:w-1/2 space-y-6">
            <h2 class="font-headline-md text-headline-md text-deep-royal">Global Reach, <span class="text-vibrant-amber">Local Expertise</span></h2>
            <p class="font-body-lg text-body-lg text-on-surface-variant">White Lotus Trading - F.Z.E operates at the intersection of international trade routes, ensuring seamless delivery of industrial hardware and organic wellness products across the MENA region and beyond.</p>
            <div class="grid grid-cols-2 gap-gutter pt-4">
                <div class="border-l-4 border-vibrant-amber pl-6">
                    <div class="font-display-lg text-headline-md text-deep-royal">15+</div>
                    <div class="font-label-caps text-label-caps text-on-surface-variant">Global Markets</div>
                </div>
                <div class="border-l-4 border-vibrant-amber pl-6">
                    <div class="font-display-lg text-headline-md text-deep-royal">200+</div>
                    <div class="font-label-caps text-label-caps text-on-surface-variant">Product Lines</div>
                </div>
            </div>
        </div>
        <div class="w-full lg:w-1/2 h-[400px] rounded-2xl overflow-hidden shadow-sm border border-divider-gray bg-surface-container-low flex items-center justify-center">
            <div class="text-center text-on-surface-variant opacity-30">
                <span class="material-symbols-outlined text-[80px]">map</span>
                <p class="font-label-caps">Global Map</p>
            </div>
        </div>
    </div>
</section>

<script>
function switchTab(category) {
    const hvacGrid = document.getElementById('grid-hvac');
    const wellnessGrid = document.getElementById('grid-wellness');
    const btnHvac = document.getElementById('btn-hvac');
    const btnWellness = document.getElementById('btn-wellness');
    if (category === 'hvac') {
        hvacGrid.classList.remove('hidden'); wellnessGrid.classList.add('hidden');
        btnHvac.classList.add('tab-active'); btnHvac.classList.remove('text-on-surface-variant');
        btnWellness.classList.remove('tab-active'); btnWellness.classList.add('text-on-surface-variant');
    } else {
        hvacGrid.classList.add('hidden'); wellnessGrid.classList.remove('hidden');
        btnWellness.classList.add('tab-active'); btnWellness.classList.remove('text-on-surface-variant');
        btnHvac.classList.remove('tab-active'); btnHvac.classList.add('text-on-surface-variant');
    }
}
</script>

<?php require_once BASE_PATH . 'includes/footer.php'; ?>
