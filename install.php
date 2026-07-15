<?php

$action = $_GET['action'] ?? '';
$message = '';

if ($action === 'install') {
    $dbPath = DB_PATH;
    if (file_exists($dbPath)) {
        unlink($dbPath);
    }
    $dir = dirname($dbPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $db = new PDO("sqlite:$dbPath", null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $db->exec('PRAGMA journal_mode=WAL');
    $db->exec('PRAGMA foreign_keys=ON');

    $db->exec('CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT NOT NULL UNIQUE, email TEXT NOT NULL UNIQUE, password_hash TEXT NOT NULL, display_name TEXT, role TEXT DEFAULT "admin", last_login DATETIME, login_attempts INTEGER DEFAULT 0, locked_until DATETIME, created_at DATETIME DEFAULT CURRENT_TIMESTAMP)');
    $db->exec('CREATE TABLE IF NOT EXISTS sessions (id TEXT PRIMARY KEY, user_id INTEGER, ip_address TEXT, user_agent TEXT, last_activity DATETIME, payload TEXT, FOREIGN KEY (user_id) REFERENCES users(id))');
    $db->exec('CREATE TABLE IF NOT EXISTS pages (id INTEGER PRIMARY KEY AUTOINCREMENT, slug TEXT NOT NULL UNIQUE, title TEXT NOT NULL, content TEXT, meta_description TEXT, status TEXT DEFAULT "draft", created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP)');
    $db->exec('CREATE TABLE IF NOT EXISTS products (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, slug TEXT NOT NULL UNIQUE, sku TEXT, division TEXT, category TEXT, description TEXT, specs TEXT, price DECIMAL(10,2), stock INTEGER DEFAULT 0, unit TEXT DEFAULT "Units", status TEXT DEFAULT "draft", image_url TEXT, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP)');
    $db->exec('CREATE TABLE IF NOT EXISTS inquiries (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, email TEXT NOT NULL, phone TEXT, company TEXT, division TEXT, subject TEXT NOT NULL, message TEXT NOT NULL, status TEXT DEFAULT "new", is_read INTEGER DEFAULT 0, admin_notes TEXT, created_at DATETIME DEFAULT CURRENT_TIMESTAMP)');
    $db->exec('CREATE TABLE IF NOT EXISTS posts (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT NOT NULL, slug TEXT NOT NULL UNIQUE, content TEXT, excerpt TEXT, featured_image TEXT, category TEXT, status TEXT DEFAULT "draft", published_at DATETIME, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP)');
    $db->exec('CREATE TABLE IF NOT EXISTS subscribers (id INTEGER PRIMARY KEY AUTOINCREMENT, email TEXT NOT NULL UNIQUE, name TEXT, status TEXT DEFAULT "active", created_at DATETIME DEFAULT CURRENT_TIMESTAMP)');
    $db->exec('CREATE TABLE IF NOT EXISTS categories (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL UNIQUE, slug TEXT NOT NULL UNIQUE, description TEXT, created_at DATETIME DEFAULT CURRENT_TIMESTAMP)');
    $db->exec('CREATE TABLE IF NOT EXISTS settings (key TEXT PRIMARY KEY, value TEXT, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP)');

    $hash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]);
    $db->prepare("INSERT INTO users (username, email, password_hash, display_name, role) VALUES (?, ?, ?, ?, ?)")->execute(['admin', 'admin@whitelotustrading.com', $hash, 'Admin User', 'super_admin']);

    $homeContent = '<section class="py-section-gap px-margin-mobile md:px-margin-desktop max-w-[1280px] mx-auto"><div class="text-center mb-16"><h2 class="font-headline-md text-headline-md text-deep-royal mb-4">Our Core Divisions</h2><div class="w-20 h-1 bg-vibrant-amber mx-auto"></div></div><div class="grid grid-cols-1 md:grid-cols-2 gap-gutter"><div class="group relative glass-card p-12 rounded-2xl overflow-hidden hover:shadow-2xl transition-all duration-500 cursor-pointer"><div class="relative z-10 space-y-6"><div class="w-16 h-16 bg-deep-royal/10 rounded-full flex items-center justify-center text-deep-royal group-hover:bg-deep-royal group-hover:text-pure-white transition-colors duration-500"><span class="material-symbols-outlined text-4xl">ac_unit</span></div><h3 class="font-headline-sm text-headline-sm text-deep-royal">Industrial HVAC Solutions</h3><p class="font-body-md text-body-md text-on-surface-variant">Precision-engineered components, ventilation systems, and climate control technology for large-scale infrastructure and industrial applications.</p><ul class="space-y-3 font-label-caps text-label-caps text-deep-royal/70"><li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm">check_circle</span> Precision Air Handling</li><li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm">check_circle</span> Energy Efficient Cooling</li><li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm">check_circle</span> Technical Maintenance</li></ul></div><div class="absolute -right-12 -bottom-12 opacity-5 group-hover:opacity-10 transition-opacity"><span class="material-symbols-outlined text-[200px]">settings_suggest</span></div></div><div class="group relative glass-card p-12 rounded-2xl overflow-hidden hover:shadow-2xl transition-all duration-500 cursor-pointer"><div class="relative z-10 space-y-6"><div class="w-16 h-16 bg-vibrant-amber/10 rounded-full flex items-center justify-center text-vibrant-amber group-hover:bg-vibrant-amber group-hover:text-charcoal-text transition-colors duration-500"><span class="material-symbols-outlined text-4xl">potted_plant</span></div><h3 class="font-headline-sm text-headline-sm text-deep-royal">Organic Wellness Trading</h3><p class="font-body-md text-body-md text-on-surface-variant">Sourcing the purest superfoods and traditional health supplements, including Himalayan Shilajit, high-grade spices, and natural wellness products.</p><ul class="space-y-3 font-label-caps text-label-caps text-deep-royal/70"><li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm">check_circle</span> Ethically Sourced Shilajit</li><li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm">check_circle</span> Premium Organic Spices</li><li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm">check_circle</span> Direct Global Supply Chain</li></ul></div><div class="absolute -right-12 -bottom-12 opacity-5 group-hover:opacity-10 transition-opacity"><span class="material-symbols-outlined text-[200px]">eco</span></div></div></div></section>';

    $insertPage = $db->prepare("INSERT INTO pages (slug, title, content, meta_description, status) VALUES (?, ?, ?, ?, 'published')");
    $insertPage->execute(['home', 'Home', $homeContent, 'White Lotus Trading - F.Z.E. - Your trusted partner in HVAC and wellness solutions across the MENA region.']);
    $insertPage->execute(['about-us', 'About Us', '<h2>Who We Are</h2><p>White Lotus Trading - F.Z.E is a premier trading company based in Dubai, United Arab Emirates, specializing in industrial HVAC solutions and organic wellness products.</p>', 'Learn about White Lotus Trading - F.Z.E.']);
    $insertPage->execute(['privacy-policy', 'Privacy Policy', '<h2>Information We Collect</h2><p>We collect information you provide directly to us.</p>', 'Privacy Policy']);
    $insertPage->execute(['terms-of-service', 'Terms of Service', '<h2>General Terms</h2><p>By accessing and using the White Lotus Trading - F.Z.E website, you agree to comply with and be bound by the following terms and conditions.</p>', 'Terms of Service']);

    $insertSetting = $db->prepare("INSERT OR IGNORE INTO settings (key, value) VALUES (?, ?)");
    $insertSetting->execute(['hero_label', 'Established Excellence']);
    $insertSetting->execute(['hero_title', 'Your Trusted Partner in <span class="text-vibrant-amber">HVAC & Wellness</span>']);
    $insertSetting->execute(['hero_subtitle', 'Bridging industrial precision with organic vitality. We provide state-of-the-art climate solutions and premium health consumables for a balanced lifestyle.']);
    $insertSetting->execute(['hero_btn_1_text', 'Explore Industrial']);
    $insertSetting->execute(['hero_btn_1_link', '/products']);
    $insertSetting->execute(['hero_btn_2_text', 'Wellness Shop']);
    $insertSetting->execute(['hero_btn_2_link', '/products?division=Consumables']);
    $insertSetting->execute(['hero_image_placeholder', 'Hero Image']);

    $insertCat = $db->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)");
    $insertCat->execute(['HVAC Systems', 'hvac-systems', 'Industrial HVAC equipment and components']);
    $insertCat->execute(['HVAC Parts', 'hvac-parts', 'Replacement parts and accessories']);
    $insertCat->execute(['Wellness Supplements', 'wellness-supplements', 'Organic health supplements and superfoods']);
    $insertCat->execute(['Spices & Herbs', 'spices-herbs', 'Premium organic spices and herbs']);

    $message = 'Database installed successfully. Admin login: admin / admin123';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install - White Lotus Trading</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f8f5f2] min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-[#1a1a2e]">White Lotus Trading</h1>
            <p class="text-[#444650] mt-2">Database Installation</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <?php if ($message): ?>
                <div class="bg-emerald-50 text-emerald-700 px-4 py-3 rounded-lg text-sm mb-6"><?= htmlspecialchars($message) ?></div>
                <a href="/admin/login" class="block w-full text-center bg-[#1a1a2e] text-white px-6 py-3 rounded-lg font-medium hover:brightness-110 transition-all">Go to Admin Login</a>
            <?php else: ?>
                <p class="text-[#444650] text-sm mb-6">This will set up the database with default data. Any existing data will be lost.</p>
                <a href="?action=install" class="block w-full text-center bg-[#1a1a2e] text-white px-6 py-3 rounded-lg font-medium hover:brightness-110 transition-all" onclick="return confirm('This will delete all existing data. Continue?')">Install Database</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
