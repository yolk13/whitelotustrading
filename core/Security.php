<?php

class Security
{
    public static function generateCsrfToken(): string
    {
        $token = bin2hex(random_bytes(32));
        Session::set('csrf_token', $token);
        return $token;
    }

    public static function csrfField(): string
    {
        $token = self::generateCsrfToken();
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    public static function validateCsrf(?string $token): bool
    {
        $stored = Session::get('csrf_token');
        if (empty($token) || empty($stored)) {
            return false;
        }
        $valid = hash_equals($stored, $token);
        Session::remove('csrf_token');
        return $valid;
    }

    public static function h(mixed $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    public static function sanitize(string $input): string
    {
        $input = strip_tags($input);
        $input = trim($input);
        return $input;
    }

    public static function sanitizeRich(string $input): string
    {
        $allowed = '<p><br><strong><em><b><i><u><ul><ol><li><h1><h2><h3><h4><blockquote><a><div><span><section><img><figure><figcaption><table><thead><tbody><tr><th><td><hr><pre><code><sup><sub>';
        return strip_tags($input, $allowed);
    }

    public static function generatePasswordHash(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function rateLimitCheck(string $identifier): bool
    {
        $row = Database::fetch(
            "SELECT login_attempts, locked_until FROM users WHERE username = ?",
            [$identifier]
        );
        if (!$row) {
            return true;
        }
        if ($row['locked_until'] && strtotime($row['locked_until']) > time()) {
            return false;
        }
        return true;
    }

    public static function rateLimitHit(string $identifier): void
    {
        $row = Database::fetch("SELECT login_attempts FROM users WHERE username = ?", [$identifier]);
        if (!$row) {
            return;
        }
        $attempts = (int)$row['login_attempts'] + 1;
        if ($attempts >= LOGIN_MAX_ATTEMPTS) {
            $lockUntil = date('Y-m-d H:i:s', time() + (LOGIN_LOCKOUT_MINUTES * 60));
            Database::query(
                "UPDATE users SET login_attempts = ?, locked_until = ? WHERE username = ?",
                [$attempts, $lockUntil, $identifier]
            );
        } else {
            Database::query(
                "UPDATE users SET login_attempts = ? WHERE username = ?",
                [$attempts, $identifier]
            );
        }
    }

    public static function rateLimitReset(string $identifier): void
    {
        Database::query(
            "UPDATE users SET login_attempts = 0, locked_until = NULL WHERE username = ?",
            [$identifier]
        );
    }

    public static function validateUpload(array $file): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return 'Upload failed with error code ' . $file['error'];
        }
        if ($file['size'] > MAX_UPLOAD_SIZE) {
            return 'File exceeds maximum size of 2MB';
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, ALLOWED_MIME_TYPES)) {
            return 'Invalid file type. Allowed: JPEG, PNG, GIF, WebP';
        }
        return null;
    }

    public static function saveUpload(array $file): ?string
    {
        $error = self::validateUpload($file);
        if ($error) {
            return null;
        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $dest = UPLOAD_PATH . $filename;
        if (!is_dir(UPLOAD_PATH)) {
            mkdir(UPLOAD_PATH, 0755, true);
        }
        move_uploaded_file($file['tmp_name'], $dest);
        return $filename;
    }

    public static function generateSlug(string $string): string
    {
        $string = mb_strtolower($string, 'UTF-8');
        $string = preg_replace('/[^\w\s-]/', '', $string);
        $string = preg_replace('/[\s-]+/', '-', $string);
        $string = trim($string, '-');
        return $string;
    }

    public static function sendSecurityHeaders(): void
    {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
        header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.tailwindcss.com https://fonts.googleapis.com https://cdnjs.cloudflare.com https://cdn.tiny.cloud 'unsafe-inline'; style-src 'self' https://fonts.googleapis.com https://cdnjs.cloudflare.com 'unsafe-inline'; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:; connect-src 'self'; form-action 'self'");
    }
}
