<?php
declare(strict_types=1);
sm_require_role(['admin', 'site_manager', 'hseq_approver']);
sm_csrf_require();

$siteId      = (int)($_POST['site_id'] ?? 0);
$code        = trim((string)($_POST['code'] ?? ''));
$name        = trim((string)($_POST['name'] ?? ''));
$description = trim((string)($_POST['description'] ?? ''));
$parentId    = (int)($_POST['parent_zone_id'] ?? 0);

if ($siteId <= 0 || $code === '' || $name === '') {
    sm_redirect('/index.php?page=dashboard&tab=zones&error=missing_fields');
}

$repo = new ZoneRepository($smPdo);
$repo->upsert($siteId, $code, $name, $description, $parentId > 0 ? $parentId : null);
(new AuditRepository($smPdo))->add((int)sm_current_user()['id'], 'zone.save', ['code' => $code]);
sm_redirect('/index.php?page=dashboard&tab=zones');
