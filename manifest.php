<?php
declare(strict_types=1);
$envFile = __DIR__ . '/.env';
if (is_file($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$k, $v] = explode('=', $line, 2);
        if (trim($k) === 'APP_BASE_URL') {
            putenv('APP_BASE_URL=' . trim($v, "\"' "));
        }
    }
}
$base = rtrim((string)(getenv('APP_BASE_URL') ?: ''), '/');
if ($base === '') {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $base = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
}
header('Content-Type: application/manifest+json; charset=utf-8');

echo json_encode([
    'name' => 'Suojainmatriisi',
    'short_name' => 'Suojain',
    'description' => 'Työmaa- ja työtehtäväkohtainen PPE-vaatimusmatriisi',
    'start_url' => $base . '/index.php?page=search',
    'display' => 'standalone',
    'theme_color' => '#0f172a',
    'background_color' => '#0f172a',
    'lang' => 'fi',
    'scope' => $base . '/',
    'icons' => [
        ['src' => $base . '/assets/img/icons/pwa-192.svg', 'sizes' => '192x192', 'type' => 'image/svg+xml', 'purpose' => 'any'],
        ['src' => $base . '/assets/img/icons/pwa-512.svg', 'sizes' => '512x512', 'type' => 'image/svg+xml', 'purpose' => 'any maskable'],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
