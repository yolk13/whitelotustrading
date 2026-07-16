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
require_once BASE_PATH . 'core/Audit.php';
require_once BASE_PATH . 'core/Translation.php';
Translation::init();

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
