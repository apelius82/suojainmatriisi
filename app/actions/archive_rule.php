<?php
declare(strict_types=1);
// Arkistoi vaatimussääntö
sm_require_role(['admin', 'manager']);
sm_csrf_require();

$id = (int)($_POST['rule_id'] ?? 0);
if ($id < 1) {
    sm_redirect('/index.php?page=dashboard&tab=rules');
}

$user = sm_current_user();
(new RequirementRepository($smPdo))->archiveRule($id, (int)$user['id']);
(new AuditRepository($smPdo))->add((int)$user['id'], 'rule.archive', ['id' => $id]);
sm_redirect('/index.php?page=dashboard&tab=rules');
