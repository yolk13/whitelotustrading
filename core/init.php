<?php

define('BASE_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);

require_once BASE_PATH . 'config.php';

require_once BASE_PATH . 'core/Database.php';
require_once BASE_PATH . 'core/Session.php';
require_once BASE_PATH . 'core/Security.php';
require_once BASE_PATH . 'core/Auth.php';
require_once BASE_PATH . 'core/Router.php';
require_once BASE_PATH . 'core/Validator.php';
require_once BASE_PATH . 'core/Model.php';
require_once BASE_PATH . 'core/Mail.php';
if (file_exists(BASE_PATH . 'core/Audit.php')) {
    require_once BASE_PATH . 'core/Audit.php';
} elseif (!class_exists('Audit')) {
    class Audit {
        public static function log(string $action, string $entityType, int $entityId, ?string $changes = null): void {}
        public static function diff(array $before, array $after): string { return ''; }
    }
}
if (file_exists(BASE_PATH . 'core/Translation.php')) {
    require_once BASE_PATH . 'core/Translation.php';
    Translation::init();
} elseif (!class_exists('Translation')) {
    class Translation {
        public static function getLocale(): string { return 'en'; }
        public static function isRtl(): bool { return false; }
        public static function dir(): string { return 'ltr'; }
        public static function t(string $key, array $replace = []): string { return $key; }
        public static function langLink(string $url, string $lang): string { return $url; }
    }
}

require_once BASE_PATH . 'includes/functions.php';

set_exception_handler(function (Throwable $e): void {
    error_log('Uncaught Exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    echo '<h1>Internal Server Error</h1><p>Something went wrong. Please try again later.</p>';
    exit;
});

set_error_handler(function (int $severity, string $message, string $file, int $line): bool {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

Session::init();
Security::sendSecurityHeaders();

$GLOBALS['old'] = $_POST;
$GLOBALS['errors'] = [];
