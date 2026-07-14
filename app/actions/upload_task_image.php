<?php
declare(strict_types=1);
// Lataa tehtävälle kansikuva (SVG, JPG, PNG, WEBP) – max 2 MB
sm_require_role(['admin', 'manager', 'site_manager']);
sm_csrf_require();

$taskId = (int)($_POST['task_id'] ?? 0);
if ($taskId < 1) {
    sm_redirect('/index.php?page=dashboard&tab=tasks&error=invalid_id');
}

// Jaettu formaatticonfig (MIME type → kanoninen tiedostopääte)
$imgCfg    = require __DIR__ . '/../../app/config/ppe_image.php';
$mimeToExt = $imgCfg['mime_to_ext'];
$maxBytes  = $imgCfg['max_bytes'];

$file = $_FILES['task_image'] ?? null;
if (!$file || !isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
    sm_redirect('/index.php?page=dashboard&tab=tasks&error=upload_failed');
}

if ($file['size'] > $maxBytes) {
    sm_redirect('/index.php?page=dashboard&tab=tasks&error=too_large');
}

// Tarkista MIME finfo:llä (ei luota $_FILES['type'] eikä tiedostopäätteeseen)
$finfo    = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file['tmp_name']);
if ($mimeType === false) {
    sm_redirect('/index.php?page=dashboard&tab=tasks&error=upload_failed');
}
if (!array_key_exists($mimeType, $mimeToExt)) {
    sm_redirect('/index.php?page=dashboard&tab=tasks&error=invalid_type');
}

// Pääte tulee validoidusta MIME-tyypistä, ei käyttäjän tiedostonnimestä
$ext = $mimeToExt[$mimeType];

$storageDir = __DIR__ . '/../../storage/task_images';
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0775, true);
}

$safeName = 'task_' . $taskId . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
$destPath = $storageDir . '/' . $safeName;
if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    sm_redirect('/index.php?page=dashboard&tab=tasks&error=move_failed');
}

(new LibraryRepository($smPdo))->updateTaskCoverImage($taskId, $safeName);
(new AuditRepository($smPdo))->add(
    (int)sm_current_user()['id'],
    'task.cover_image_upload',
    ['id' => $taskId, 'file' => $safeName]
);

sm_redirect('/index.php?page=dashboard&tab=tasks');
