<?php
declare(strict_types=1);
sm_require_role(['admin', 'manager', 'site_manager']);
sm_csrf_require();
(new LibraryRepository($smPdo))->upsertWorker(trim((string)$_POST['full_name']), (int)$_POST['site_id'], (int)$_POST['task_id']);
(new AuditRepository($smPdo))->add((int)sm_current_user()['id'], 'worker.save', ['name' => $_POST['full_name'] ?? '']);
sm_redirect('/index.php?page=dashboard');
