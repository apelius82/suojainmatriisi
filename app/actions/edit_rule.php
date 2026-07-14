<?php
declare(strict_types=1);
// Muokkaa olemassa olevaa vaatimussääntöä
sm_require_role(['admin', 'manager']);
sm_csrf_require();

$id = (int)($_POST['rule_id'] ?? 0);
if ($id < 1) {
    sm_redirect('/index.php?page=dashboard&tab=rules');
}

$user = sm_current_user();
$rule = [
    'scope_type'          => trim((string)$_POST['scope_type']),
    'environment_id'      => (int)($_POST['environment_id'] ?? 0),
    'site_id'             => (int)($_POST['site_id'] ?? 0),
    'zone_id'             => (int)($_POST['zone_id'] ?? 0),
    'task_id'             => (int)($_POST['task_id'] ?? 0),
    'ppe_item_id'         => (int)($_POST['ppe_item_id'] ?? 0),
    'requirement_level'   => trim((string)$_POST['requirement_level']),
    'notes'               => trim((string)($_POST['notes'] ?? '')),
    'condition_text'      => trim((string)($_POST['condition_text'] ?? '')),
    'change_description'  => trim((string)($_POST['change_description'] ?? '')),
];

(new RequirementRepository($smPdo))->updateRule($id, $rule, (int)$user['id']);
(new AuditRepository($smPdo))->add((int)$user['id'], 'rule.edit', ['id' => $id]);
sm_redirect('/index.php?page=dashboard&tab=rules');
