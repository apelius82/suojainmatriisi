<?php
declare(strict_types=1);
sm_require_role(['admin', 'manager', 'site_manager']);
sm_csrf_require();
$envId = (int)($_POST['environment_id'] ?? 0);
(new LibraryRepository($smPdo))->upsertSite(
    trim((string)$_POST['name']),
    trim((string)$_POST['code']),
    $envId > 0 ? $envId : null
);
(new AuditRepository($smPdo))->add((int)sm_current_user()['id'], 'site.save', ['code' => $_POST['code'] ?? '']);
sm_redirect('/index.php?page=dashboard&tab=sites');
