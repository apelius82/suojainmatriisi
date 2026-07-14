<?php
declare(strict_types=1);
sm_require_role(['admin', 'manager']);
sm_csrf_require();
(new LibraryRepository($smPdo))->upsertTask(trim((string)$_POST['name']), trim((string)$_POST['category']));
(new AuditRepository($smPdo))->add((int)sm_current_user()['id'], 'task.save', ['name' => $_POST['name'] ?? '']);
sm_redirect('/index.php?page=dashboard');
