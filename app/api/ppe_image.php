<?php
declare(strict_types=1);
// Palvelee PPE-kuvat storage-kansiosta turvallisesti
require_once __DIR__ . '/../../app/includes/bootstrap.php';

sm_require_login();

$file = basename((string)($_GET['f'] ?? ''));
if ($file === '' || !preg_match('/^ppe_\d+_[0-9a-f]+\.(svg|jpg|png|webp)$/', $file)) {
    http_response_code(400);
    exit;
}

$path = __DIR__ . '/../../storage/ppe_images/' . $file;
if (!is_file($path)) {
    http_response_code(404);
    exit;
}

$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
$mimeMap = [
    'svg'  => 'image/svg+xml',
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png'  => 'image/png',
    'webp' => 'image/webp',
];
$mime = $mimeMap[$ext] ?? 'application/octet-stream';

header('Content-Type: ' . $mime);
header('Cache-Control: private, max-age=86400');
$fileSize = filesize($path);
if ($fileSize !== false) {
    header('Content-Length: ' . $fileSize);
}
readfile($path);
