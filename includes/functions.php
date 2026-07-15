<?php

function old(string $key, string $default = ''): string
{
    return Security::h($GLOBALS['old'][$key] ?? $default);
}

function error(string $key): string
{
    return $GLOBALS['errors'][$key] ?? '';
}

function hasError(string $key): bool
{
    return isset($GLOBALS['errors'][$key]);
}

function setError(string $key, string $message): void
{
    $GLOBALS['errors'][$key] = $message;
}

 function setOld(array $data): void
{
    $GLOBALS['old'] = $data;
}

function flash(string $key, ?string $value = null): ?string
{
    if ($value !== null) {
        Session::set('flash_' . $key, $value);
        return null;
    }
    $val = Session::get('flash_' . $key);
    Session::remove('flash_' . $key);
    return $val;
}

function hasFlash(string $key): bool
{
    return Session::has('flash_' . $key);
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function redirectBack(): never
{
    $referer = $_SERVER['HTTP_REFERER'] ?? '/';
    $host = parse_url($referer, PHP_URL_HOST);
    if ($host !== null && $host !== ($_SERVER['HTTP_HOST'] ?? '')) {
        $referer = '/';
    }
    redirect($referer);
}

function asset(string $path): string
{
    return '/assets/' . ltrim($path, '/');
}

function uploadUrl(?string $filename): string
{
    if (!$filename) {
        return '/assets/images/placeholder.svg';
    }
    return '/' . UPLOAD_URL . $filename;
}

function excerpt(string $text, int $length = 120): string
{
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . '...';
}

function formatDate(string $date, string $format = 'M d, Y'): string
{
    return date($format, strtotime($date));
}

function selected(string $value, string $compare): string
{
    return $value === $compare ? 'selected' : '';
}

function checked(string $value, string $compare): string
{
    return $value === $compare ? 'checked' : '';
}

function isActive(string $path): string
{
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    return $uri === $path ? 'tab-active' : '';
}

function paginationLinks(array $paginator, string $baseUrl): string
{
    if ($paginator['totalPages'] <= 1) {
        return '';
    }
    $html = '<div class="flex gap-2">';
    if ($paginator['hasPrev']) {
        $html .= '<a href="' . $baseUrl . '?page=' . $paginator['prevPage'] . '" class="w-8 h-8 flex items-center justify-center rounded border border-divider-gray hover:bg-surface-container transition-colors"><span class="material-symbols-outlined">chevron_left</span></a>';
    }
    for ($i = 1; $i <= $paginator['totalPages']; $i++) {
        $active = $i === $paginator['page'] ? 'bg-deep-royal text-pure-white' : 'border border-divider-gray hover:bg-surface-container';
        $html .= '<a href="' . $baseUrl . '?page=' . $i . '" class="w-8 h-8 flex items-center justify-center rounded ' . $active . ' transition-colors">' . $i . '</a>';
    }
    if ($paginator['hasNext']) {
        $html .= '<a href="' . $baseUrl . '?page=' . $paginator['nextPage'] . '" class="w-8 h-8 flex items-center justify-center rounded border border-divider-gray hover:bg-surface-container transition-colors"><span class="material-symbols-outlined">chevron_right</span></a>';
    }
    $html .= '</div>';
    return $html;
}
