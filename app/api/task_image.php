<?php
declare(strict_types=1);
// Palvelee tehtäväkuvia storage-kansiosta turvallisesti
require_once __DIR__ . '/../../app/includes/bootstrap.php';

sm_require_login();

// Jaettu formaatticonfig (tiedostopääte → MIME type)
$imgCfg    = require __DIR__ . '/../../app/config/ppe_image.php';
$extToMime = $imgCfg['ext_to_mime'];

// Rakenna tiedostonimen validointireges dynaamisesti sallituista päätteistä
$extPattern = implode('|', array_map(fn($k) => preg_quote($k, '/'), array_keys($extToMime)));
$file = basename((string)($_GET['f'] ?? ''));
if ($file === '' || !preg_match('/^task_\d+_[0-9a-f]+\.(' . $extPattern . ')$/', $file)) {
    http_response_code(400);
    exit;
}

$path = __DIR__ . '/../../storage/task_images/' . $file;
if (!is_file($path)) {
    http_response_code(404);
    exit;
}

$ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));
$mime = $extToMime[$ext] ?? 'application/octet-stream';

header('Content-Type: ' . $mime);
header('Cache-Control: private, max-age=86400');
$fileSize = filesize($path);
if ($fileSize !== false) {
    header('Content-Length: ' . $fileSize);
}
readfile($path);
