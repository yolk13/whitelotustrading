<?php

if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . DIRECTORY_SEPARATOR);
}
define('DB_PATH', BASE_PATH . 'data' . DIRECTORY_SEPARATOR . 'whitelotus.db');
define('UPLOAD_PATH', BASE_PATH . 'uploads' . DIRECTORY_SEPARATOR);
define('UPLOAD_URL', 'uploads/');

define('SITE_NAME', 'White Lotus Trading - F.Z.E.');
define('SITE_URL', 'http://localhost:8080');

define('ADMIN_EMAIL', 'admin@whitelotustrading.com');

define('SESSION_LIFETIME', 7200);
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_MINUTES', 15);
define('BCRYPT_COST', 12);

define('MAX_UPLOAD_SIZE', 2097152);
define('ALLOWED_MIME_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

date_default_timezone_set('Asia/Dubai');
