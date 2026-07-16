<?php

class Router
{
    private static array $routes = [];

    public static function add(string $method, string $pattern, string $handler): void
    {
        self::$routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    public static function dispatch(string $uri, string $method = 'GET'): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        if ($uri === '' || $uri === false) {
            $uri = '/';
        }
        if ($uri !== '/') {
            $uri = rtrim($uri, '/');
        }
        if ($uri !== '' && $uri[0] !== '/') {
            $uri = '/' . $uri;
        }

        foreach (self::$routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $route['pattern']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                self::loadHandler($route['handler'], $params);
                return;
            }
        }

        self::loadHandler('public/home.php', []);
    }

    private static function loadHandler(string $handler, array $params): void
    {
        $path = BASE_PATH . $handler;
        if (!file_exists($path)) {
            http_response_code(404);
            echo '<h1>404 - Page Not Found</h1>';
            exit;
        }
        extract($params);
        require $path;
    }

    public static function registerRoutes(): void
    {
        self::add('GET', '/', 'public/home.php');
        self::add('GET', '/products', 'public/products.php');
        self::add('GET', '/product/{slug}', 'public/product-detail.php');
        self::add('GET', '/search', 'public/search.php');
        self::add('GET', '/sitemap.xml', 'public/sitemap.php');
        self::add('GET', '/inquiry', 'public/inquiry.php');
        self::add('POST', '/inquiry', 'public/inquiry.php');
        self::add('GET', '/contact', 'public/contact.php');
        self::add('POST', '/contact', 'public/contact.php');
        self::add('GET', '/blog', 'public/blog.php');
        self::add('GET', '/blog/{slug}', 'public/blog-detail.php');
        self::add('GET', '/unsubscribe', 'public/unsubscribe.php');
        self::add('GET', '/page/{slug}', 'public/page.php');
        self::add('GET', '/admin/login', 'admin/login.php');
        self::add('POST', '/admin/login', 'admin/login.php');
        self::add('POST', '/admin/logout', 'admin/logout.php');

        $adminRoutes = [
            '/admin' => 'admin/dashboard.php',
            '/admin/products' => 'admin/products-handler.php',
            '/admin/inquiries' => 'admin/inquiries-handler.php',
            '/admin/pages' => 'admin/pages-handler.php',
            '/admin/blog' => 'admin/posts-handler.php',
            '/admin/upload-image' => 'admin/upload-image.php',
            '/admin/media' => 'admin/media-handler.php',
            '/admin/subscribers' => 'admin/subscribers-handler.php',
            '/admin/categories' => 'admin/categories-handler.php',
            '/admin/settings' => 'admin/settings-handler.php',
            '/admin/audit' => 'admin/audit-handler.php',
            '/admin/trash' => 'admin/trash-handler.php',
            '/admin/specs' => 'admin/specs-handler.php',
            '/admin/users' => 'admin/users-handler.php',
        ];

        foreach ($adminRoutes as $pattern => $handler) {
            self::add('GET', $pattern, $handler);
            self::add('POST', $pattern, $handler);
        }
    }
}
