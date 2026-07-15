<?php

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dbPath = DB_PATH;
            $dir = dirname($dbPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            self::$instance = new PDO("sqlite:$dbPath", null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            self::$instance->exec('PRAGMA journal_mode=WAL');
            self::$instance->exec('PRAGMA foreign_keys=ON');
            self::initializeSchema();
        }
        return self::$instance;
    }

    private static function initializeSchema(): void
    {
        self::$instance->exec('
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                email TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL,
                display_name TEXT,
                role TEXT DEFAULT "admin",
                last_login DATETIME,
                login_attempts INTEGER DEFAULT 0,
                locked_until DATETIME,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');
        self::$instance->exec('
            CREATE TABLE IF NOT EXISTS sessions (
                id TEXT PRIMARY KEY,
                user_id INTEGER,
                ip_address TEXT,
                user_agent TEXT,
                last_activity DATETIME,
                payload TEXT,
                FOREIGN KEY (user_id) REFERENCES users(id)
            )
        ');
        self::$instance->exec('
            CREATE TABLE IF NOT EXISTS pages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                slug TEXT NOT NULL UNIQUE,
                title TEXT NOT NULL,
                content TEXT,
                meta_description TEXT,
                status TEXT DEFAULT "draft",
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');
        self::$instance->exec('
            CREATE TABLE IF NOT EXISTS products (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                slug TEXT NOT NULL UNIQUE,
                sku TEXT,
                division TEXT,
                category TEXT,
                description TEXT,
                specs TEXT,
                price DECIMAL(10,2),
                stock INTEGER DEFAULT 0,
                unit TEXT DEFAULT "Units",
                status TEXT DEFAULT "draft",
                image_url TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');
        self::$instance->exec('
            CREATE TABLE IF NOT EXISTS inquiries (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                phone TEXT,
                company TEXT,
                division TEXT,
                subject TEXT NOT NULL,
                message TEXT NOT NULL,
                status TEXT DEFAULT "new",
                is_read INTEGER DEFAULT 0,
                admin_notes TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');
        self::$instance->exec('
            CREATE TABLE IF NOT EXISTS posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                slug TEXT NOT NULL UNIQUE,
                content TEXT,
                excerpt TEXT,
                featured_image TEXT,
                category TEXT,
                status TEXT DEFAULT "draft",
                published_at DATETIME,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');

        $existingAdmin = self::fetch("SELECT id FROM users WHERE username = 'admin'");
        if (!$existingAdmin) {
            $hash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]);
            self::insert('users', [
                'username' => 'admin',
                'email' => 'admin@whitelotustrading.com',
                'password_hash' => $hash,
                'display_name' => 'Admin User',
                'role' => 'super_admin',
            ]);
        }

        $existingAbout = self::fetch("SELECT id FROM pages WHERE slug = 'about-us'");
        if (!$existingAbout) {
            self::insert('pages', [
                'slug' => 'about-us',
                'title' => 'About Us',
                'content' => '<h2>Who We Are</h2><p>White Lotus Trading - F.Z.E is a premier trading company based in Dubai, United Arab Emirates, specializing in industrial HVAC solutions and organic wellness products. We bridge the gap between industrial precision and organic vitality, delivering state-of-the-art climate solutions alongside premium health consumables.</p><h2>Our Mission</h2><p>To provide exceptional products and services that enhance both industrial efficiency and personal well-being, fostering sustainable growth across the MENA region and beyond.</p><h2>Our Values</h2><ul><li><strong>Integrity:</strong> We conduct business with transparency and ethical responsibility.</li><li><strong>Excellence:</strong> We strive for the highest quality in every product and service.</li><li><strong>Innovation:</strong> We embrace cutting-edge technology and sustainable practices.</li><li><strong>Partnership:</strong> We build lasting relationships with clients and suppliers worldwide.</li></ul>',
                'meta_description' => 'Learn about White Lotus Trading - F.Z.E, a Dubai-based trading company specializing in HVAC solutions and organic wellness products.',
                'status' => 'published',
            ]);
        }

        $existingPrivacy = self::fetch("SELECT id FROM pages WHERE slug = 'privacy-policy'");
        if (!$existingPrivacy) {
            self::insert('pages', [
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'content' => '<h2>Information We Collect</h2><p>We collect information you provide directly to us, such as your name, email address, phone number, and company details when you submit an inquiry or contact form on our website.</p><h2>How We Use Your Information</h2><p>We use the information we collect to respond to your inquiries, process your requests, improve our services, and communicate with you about our products and offerings.</p><h2>Data Protection</h2><p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p><h2>Third-Party Disclosure</h2><p>We do not sell, trade, or otherwise transfer your personal information to third parties without your consent, except as required by law.</p><h2>Contact Us</h2><p>If you have any questions about this Privacy Policy, please contact us through our inquiry form or reach out to our team directly.</p>',
                'meta_description' => 'Privacy Policy for White Lotus Trading - F.Z.E. Learn how we collect, use, and protect your personal information.',
                'status' => 'published',
            ]);
        }

        $existingTerms = self::fetch("SELECT id FROM pages WHERE slug = 'terms-of-service'");
        if (!$existingTerms) {
            self::insert('pages', [
                'slug' => 'terms-of-service',
                'title' => 'Terms of Service',
                'content' => '<h2>General Terms</h2><p>By accessing and using the White Lotus Trading - F.Z.E website, you agree to comply with and be bound by the following terms and conditions.</p><h2>Products and Services</h2><p>All products and services are subject to availability. We reserve the right to modify or discontinue any product or service without prior notice. Prices are subject to change without notice.</p><h2>Intellectual Property</h2><p>All content, trademarks, and intellectual property on this website are owned by White Lotus Trading - F.Z.E unless otherwise stated. Unauthorized use is prohibited.</p><h2>Limitation of Liability</h2><p>White Lotus Trading - F.Z.E shall not be liable for any direct, indirect, incidental, or consequential damages arising from the use of our products or services.</p><h2>Governing Law</h2><p>These terms shall be governed by and construed in accordance with the laws of the United Arab Emirates.</p>',
                'meta_description' => 'Terms of Service for White Lotus Trading - F.Z.E. Understand the conditions governing the use of our website and services.',
                'status' => 'published',
            ]);
        }
    }

    public static function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetch(string $sql, array $params = []): ?array
    {
        $result = self::query($sql, $params)->fetch();
        return $result ?: null;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    public static function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        self::query("INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})", array_values($data));
        return (int)self::getInstance()->lastInsertId();
    }

    public static function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $sets = implode(', ', array_map(fn($col) => "{$col} = ?", array_keys($data)));
        $params = array_merge(array_values($data), $whereParams);
        self::query("UPDATE {$table} SET {$sets} WHERE {$where}", $params);
        return self::query("SELECT changes()")->fetchColumn();
    }

    public static function delete(string $table, string $where, array $params = []): int
    {
        self::query("DELETE FROM {$table} WHERE {$where}", $params);
        return self::query("SELECT changes()")->fetchColumn();
    }

    public static function exists(string $table, string $column, mixed $value): bool
    {
        return (bool)self::fetch(
            "SELECT 1 FROM {$table} WHERE {$column} = ? LIMIT 1",
            [$value]
        );
    }

    public static function count(string $table, string $where = '1=1', array $params = []): int
    {
        return (int)self::fetch("SELECT COUNT(*) as cnt FROM {$table} WHERE {$where}", $params)['cnt'];
    }
}
