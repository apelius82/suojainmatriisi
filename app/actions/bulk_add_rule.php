<?php
declare(strict_types=1);
// Massalisäys: lisää sama vaatimus usealle tehtävälle (kaikki työmaan/ympäristön tehtävät tai valitut)
sm_require_role(['admin', 'manager']);
sm_csrf_require();

$user    = sm_current_user();
$library = new LibraryRepository($smPdo);
$reqRepo = new RequirementRepository($smPdo);
$audit   = new AuditRepository($smPdo);

$ppeId    = (int)($_POST['ppe_item_id'] ?? 0);
$level    = trim((string)($_POST['requirement_level'] ?? 'mandatory'));
$cond     = trim((string)($_POST['condition_text'] ?? ''));
$notes    = trim((string)($_POST['notes'] ?? ''));
$envId    = (int)($_POST['environment_id'] ?? 0);
$siteId   = (int)($_POST['site_id'] ?? 0);
$zoneId   = (int)($_POST['zone_id'] ?? 0);
$status   = 'draft';
$taskIds  = (array)($_POST['task_ids'] ?? []);

if ($ppeId < 1) {
    sm_redirect('/index.php?page=dashboard&tab=rules&error=invalid_ppe');
}

// Jos ei valittuja tehtäviä, hae kaikki työmaan / ympäristön tehtävät
if (empty($taskIds)) {
    $allTasks = $library->allTasks($envId > 0 ? $envId : null);
    $taskIds = array_column($allTasks, 'id');
}

if (empty($taskIds)) {
    sm_redirect('/index.php?page=dashboard&tab=rules&error=no_tasks');
}

$scopeType = RequirementRepository::deriveScopeType($siteId, $envId);

$baseRule = [
    'scope_type'         => $scopeType,
    'environment_id'     => $envId ?: 0,
    'site_id'            => $siteId ?: 0,
    'zone_id'            => $zoneId ?: 0,
    'task_id'            => 0,
    'ppe_item_id'        => $ppeId,
    'requirement_level'  => $level,
    'status'             => $status,
    'notes'              => $notes,
    'condition_text'     => $cond,
    'change_description' => 'Massalisäys',
];

$count = $reqRepo->bulkAddRule($baseRule, $taskIds, (int)$user['id']);
$audit->add((int)$user['id'], 'rule.bulk_add', [
    'ppe_id'   => $ppeId,
    'env_id'   => $envId,
    'site_id'  => $siteId,
    'count'    => $count,
]);

sm_redirect('/index.php?page=dashboard&tab=rules&bulk_added=' . $count);
