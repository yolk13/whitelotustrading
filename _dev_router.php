<?php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($uri !== '/' && is_file(__DIR__ . $uri)) {
    return false;
}

$_GET['route'] = ltrim($uri, '/');
require __DIR__ . '/index.php';
