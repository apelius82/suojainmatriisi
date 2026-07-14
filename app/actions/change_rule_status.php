<?php
declare(strict_types=1);
sm_require_role(['admin', 'reviewer', 'hseq_reviewer', 'hseq_approver', 'site_manager']);
sm_csrf_require();
$repo = new RequirementRepository($smPdo);
$workflow = new WorkflowService();
$ruleId = (int)$_POST['rule_id'];
$to = (string)$_POST['status'];
$rule = $repo->findRule($ruleId);
if ($rule && $workflow->canTransition((string)$rule['status'], $to)) {
    $repo->changeStatus($ruleId, $to, (int)sm_current_user()['id']);
    (new AuditRepository($smPdo))->add((int)sm_current_user()['id'], 'rule.transition', ['rule_id' => $ruleId, 'to' => $to]);
}
sm_redirect('/index.php?page=dashboard&tab=rules');
