<?php

require_once __DIR__ . '/core/init.php';

require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Page.php';
require_once __DIR__ . '/models/Product.php';
require_once __DIR__ . '/models/Inquiry.php';
require_once __DIR__ . '/models/Post.php';

$route = ltrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/') ?: '/';

Router::registerRoutes();
Router::dispatch($route, $_SERVER['REQUEST_METHOD']);
