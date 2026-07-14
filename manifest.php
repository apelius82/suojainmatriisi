<?php
declare(strict_types=1);
require_once __DIR__ . '/app/includes/helpers.php';
sm_env_load(__DIR__ . '/.env');
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
