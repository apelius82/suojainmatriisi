<?php
declare(strict_types=1);
// Lataa PPE-varusteelle kuva (SVG, JPG, PNG, WEBP) – max 2 MB
sm_require_role(['admin', 'manager', 'site_manager']);
sm_csrf_require();

$ppeId = (int)($_POST['ppe_item_id'] ?? 0);
if ($ppeId < 1) {
    sm_redirect('/index.php?page=dashboard&tab=ppe&error=invalid_id');
}

// MIME type → canonical extension mapping (extension derived from validated MIME, not filename)
$mimeToExt = [
    'image/svg+xml' => 'svg',
    'image/jpeg'    => 'jpg',
    'image/png'     => 'png',
    'image/webp'    => 'webp',
];
$maxBytes = 2 * 1024 * 1024; // 2 MB

$file = $_FILES['ppe_image'] ?? null;
if (!$file || !isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
    sm_redirect('/index.php?page=dashboard&tab=ppe&error=upload_failed');
}

if ($file['size'] > $maxBytes) {
    sm_redirect('/index.php?page=dashboard&tab=ppe&error=too_large');
}

// Tarkista MIME finfo:llä (ei luota $_FILES['type'] eikä tiedostopäätteeseen)
$finfo    = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file['tmp_name']);
if (!array_key_exists($mimeType, $mimeToExt)) {
    sm_redirect('/index.php?page=dashboard&tab=ppe&error=invalid_type');
}

// Pääte tulee validoidusta MIME-tyypistä, ei käyttäjän tiedostonnimestä
$ext = $mimeToExt[$mimeType];

$storageDir = __DIR__ . '/../../storage/ppe_images';
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0775, true);
}

$safeName = 'ppe_' . $ppeId . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
$destPath = $storageDir . '/' . $safeName;
if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    sm_redirect('/index.php?page=dashboard&tab=ppe&error=move_failed');
}

(new LibraryRepository($smPdo))->updatePpeItemImage($ppeId, $safeName);
(new AuditRepository($smPdo))->add(
    (int)sm_current_user()['id'],
    'ppe.image_upload',
    ['id' => $ppeId, 'file' => $safeName]
);

sm_redirect('/index.php?page=dashboard&tab=ppe');
