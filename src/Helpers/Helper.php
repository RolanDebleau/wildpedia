<?php

namespace App\Helpers;

class Helper
{
    // Escape HTML untuk mencegah XSS
    public static function e(mixed $value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
    }

    // Buat URL dengan query string
    public static function url(string $path, array $query = []): string
    {
        $base = rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');

        return $query ? $base . '?' . http_build_query($query) : $base;
    }

    // Redirect
    public static function redirect(string $path): never
    {
        header('Location: ' . self::url($path));
        exit;
    }

    // Ambil nilai dari array, default ''
    public static function input(array $source, string $key, string $default = ''): string
    {
        return trim($source[$key] ?? $default);
    }

    // Potong string
    public static function limit(string $text, int $length = 100): string
    {
        return mb_strlen($text) > $length
            ? mb_substr($text, 0, $length) . '...'
            : $text;
    }

    // Slug sederhana
    public static function slug(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', trim($text));

        return $text;
    }

    // Build pagination query string
    public static function paginateUrl(string $path, array $filters, int $page): string
    {
        $query = array_filter($filters) + ['page' => $page];

        return self::url($path, $query);
    }

    // CSRF token
    public static function csrfToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrf(): bool
    {
        $token = $_POST['_token'] ?? '';

        return isset($_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $token);
    }

    // Flash message
    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['flash'][$key] = $value;
    }

    public static function getFlash(string $key): mixed
    {
        $value = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);

        return $value;
    }

    // IP pengguna
    public static function ip(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
