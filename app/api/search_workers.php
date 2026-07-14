<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
sm_require_login();
header('Content-Type: application/json; charset=utf-8');
$siteId = (int)($_GET['site_id'] ?? 0);
$taskId = (int)($_GET['task_id'] ?? 0);
$q = mb_strtolower(trim((string)($_GET['q'] ?? '')));
$workers = (new LibraryRepository($smPdo))->allWorkers();
$filtered = array_values(array_filter($workers, function (array $w) use ($siteId, $taskId, $q): bool {
    if ($siteId > 0 && (int)($w['site_id'] ?? 0) !== $siteId) {
        return false;
    }
    if ($taskId > 0 && (int)($w['task_id'] ?? 0) !== $taskId) {
        return false;
    }
    if ($q !== '' && !str_contains(mb_strtolower((string)$w['full_name']), $q)) {
        return false;
    }
    return true;
}));
echo json_encode(['workers' => $filtered], JSON_UNESCAPED_UNICODE);
