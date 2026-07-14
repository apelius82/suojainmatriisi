<?php
declare(strict_types=1);
require_once __DIR__ . '/../../app/includes/bootstrap.php';

sm_require_login();
header('Content-Type: application/json; charset=utf-8');

$siteId = (int)($_GET['site_id'] ?? 0);
if ($siteId <= 0) {
    echo json_encode([]);
    exit;
}

$zoneRepo = new ZoneRepository($smPdo);
echo json_encode($zoneRepo->allBySite($siteId), JSON_UNESCAPED_UNICODE);
