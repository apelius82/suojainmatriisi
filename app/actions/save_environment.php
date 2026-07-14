<?php
declare(strict_types=1);
sm_require_role(['admin', 'site_manager', 'hseq_approver']);
sm_csrf_require();

$code        = trim((string)($_POST['code'] ?? ''));
$name        = trim((string)($_POST['name'] ?? ''));
$description = trim((string)($_POST['description'] ?? ''));

if ($code === '' || $name === '') {
    sm_redirect('/index.php?page=dashboard&tab=environments&error=missing_fields');
}

$repo = new EnvironmentRepository($smPdo);
$repo->upsert($code, $name, $description);
(new AuditRepository($smPdo))->add((int)sm_current_user()['id'], 'environment.save', ['code' => $code]);
sm_redirect('/index.php?page=dashboard&tab=environments');
