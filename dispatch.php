<?php

require_once __DIR__ . '/core/init.php';

require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Page.php';
require_once __DIR__ . '/models/Product.php';
require_once __DIR__ . '/models/Inquiry.php';
require_once __DIR__ . '/models/Post.php';
require_once __DIR__ . '/models/Category.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$uri = rtrim($uri, '/');

$directRoutes = [
    '/admin/media' => __DIR__ . '/admin/media-handler.php',
    '/admin/subscribers' => __DIR__ . '/admin/subscribers-handler.php',
    '/admin/categories' => __DIR__ . '/admin/categories-handler.php',
    '/admin/settings' => __DIR__ . '/admin/settings-handler.php',
];
if (isset($directRoutes[$uri])) {
    http_response_code(200);
    require $directRoutes[$uri];
    exit;
}

$route = ltrim($uri, '/') ?: '/';

Router::registerRoutes();
Router::dispatch($route, $_SERVER['REQUEST_METHOD']);
