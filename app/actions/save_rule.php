<?php
declare(strict_types=1);
sm_require_role(['admin', 'reviewer', 'hseq_reviewer', 'hseq_approver']);
sm_csrf_require();
$repo = new RequirementRepository($smPdo);
$id = $repo->addRule([
    'scope_type'        => (string)$_POST['scope_type'],
    'environment_id'    => (int)($_POST['environment_id'] ?? 0),
    'site_id'           => (int)($_POST['site_id'] ?? 0),
    'zone_id'           => (int)($_POST['zone_id'] ?? 0),
    'task_id'           => (int)($_POST['task_id'] ?? 0),
    'ppe_item_id'       => (int)$_POST['ppe_item_id'],
    'requirement_level' => (string)$_POST['requirement_level'],
    'status'            => (string)($_POST['status'] ?? 'draft'),
    'notes'             => trim((string)($_POST['notes'] ?? '')),
    'condition_text'    => trim((string)($_POST['condition_text'] ?? '')),
    'change_description'=> trim((string)($_POST['change_description'] ?? '')),
], (int)sm_current_user()['id']);
(new AuditRepository($smPdo))->add((int)sm_current_user()['id'], 'rule.save', ['rule_id' => $id]);
sm_redirect('/index.php?page=dashboard&tab=rules');
