<?php
declare(strict_types=1);
sm_require_role(['admin', 'manager', 'site_manager']);
sm_csrf_require();
$library = new LibraryRepository($smPdo);
$library->upsertPpeItem(
    trim((string)$_POST['code']),
    trim((string)$_POST['name']),
    trim((string)$_POST['category']),
    trim((string)$_POST['icon']),
    trim((string)($_POST['item_class'] ?? 'personal_protection')),
    trim((string)($_POST['standard_ref'] ?? ''))
);
$audit = new AuditRepository($smPdo);
$userId = (int)sm_current_user()['id'];

// Uuden suojaimen liitos suoraan lisäyslomakkeelta (yksittäinen tai massalisäys)
$linkedLevel = trim((string)($_POST['link_requirement_level'] ?? ''));
$linkTaskId  = (int)($_POST['link_task_id'] ?? 0);
$linkEnvId   = (int)($_POST['link_environment_id'] ?? 0);
$linkSiteId  = (int)($_POST['link_site_id'] ?? 0);
$linkZoneId  = (int)($_POST['link_zone_id'] ?? 0);
$linkNotes   = trim((string)($_POST['link_notes'] ?? ''));
$applyAll    = (int)($_POST['link_apply_all_site_tasks'] ?? 0) === 1;

if ($linkedLevel !== '') {
    $item = $library->findPpeItemByCode(trim((string)$_POST['code']));
    $ppeId = (int)($item['id'] ?? 0);
    if ($ppeId > 0) {
        $reqRepo = new RequirementRepository($smPdo);
        if ($applyAll && $linkSiteId > 0) {
            $tasks = $library->allTasks($linkEnvId > 0 ? $linkEnvId : null);
            $taskIds = array_map(static fn(array $t): int => (int)$t['id'], $tasks);
            if (!empty($taskIds)) {
                $baseRule = [
                    'scope_type'         => RequirementRepository::deriveScopeType($linkSiteId, $linkEnvId),
                    'environment_id'     => $linkEnvId,
                    'site_id'            => $linkSiteId,
                    // bulkAddRule johtaa task-kohtaiset scope-tyypit (site_task/task), joten zone ei ole mukana tässä haarassa
                    'zone_id'            => 0,
                    'task_id'            => 0,
                    'ppe_item_id'        => $ppeId,
                    'requirement_level'  => $linkedLevel,
                    'status'             => 'draft',
                    'notes'              => $linkNotes,
                    'condition_text'     => '',
                    'change_description' => 'Suojaimen lisäyslomakkeelta',
                ];
                $reqRepo->bulkAddRule($baseRule, $taskIds, $userId);
            }
        } elseif ($linkTaskId > 0) {
            $scopeType = 'task';
            if ($linkZoneId > 0) {
                $scopeType = 'zone_task';
            } elseif ($linkSiteId > 0) {
                $scopeType = 'site_task';
            }
            $reqRepo->addRule([
                'scope_type'         => $scopeType,
                'environment_id'     => $linkEnvId,
                'site_id'            => $linkSiteId,
                'zone_id'            => $linkZoneId,
                'task_id'            => $linkTaskId,
                'ppe_item_id'        => $ppeId,
                'requirement_level'  => $linkedLevel,
                'status'             => 'draft',
                'notes'              => $linkNotes,
                'condition_text'     => '',
                'change_description' => 'Suojaimen lisäyslomakkeelta',
            ], $userId);
        }
    }
}

$audit->add($userId, 'ppe.save', ['code' => $_POST['code'] ?? '']);
sm_redirect('/index.php?page=dashboard&tab=ppe');
