<?php

class Translation
{
    private static ?string $locale = null;
    private static array $strings = [];

    public static function init(): void
    {
        $supported = ['en', 'ar'];
        $locale = $_GET['lang'] ?? $_COOKIE['lang'] ?? 'en';
        if (!in_array($locale, $supported)) {
            $locale = 'en';
        }
        self::$locale = $locale;
        setcookie('lang', $locale, time() + 86400 * 365, '/', '', false, false);

        $file = BASE_PATH . "lang/{$locale}.json";
        if (file_exists($file)) {
            self::$strings = json_decode(file_get_contents($file), true) ?? [];
        }
    }

    public static function getLocale(): string
    {
        return self::$locale ?? 'en';
    }

    public static function isRtl(): bool
    {
        return self::$locale === 'ar';
    }

    public static function dir(): string
    {
        return self::isRtl() ? 'rtl' : 'ltr';
    }

    public static function t(string $key, array $replace = []): string
    {
        $value = self::$strings[$key] ?? $key;
        foreach ($replace as $k => $v) {
            $value = str_replace('{' . $k . '}', $v, $value);
        }
        return $value;
    }

    public static function langLink(string $url, string $lang): string
    {
        $query = parse_url($url, PHP_URL_QUERY);
        $params = [];
        if ($query) {
            parse_str($query, $params);
        }
        $params['lang'] = $lang;
        return strtok($url, '?') . '?' . http_build_query($params);
    }
}
