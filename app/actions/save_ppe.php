<?php
declare(strict_types=1);
sm_require_role(['admin', 'manager']);
sm_csrf_require();
(new LibraryRepository($smPdo))->upsertPpeItem(trim((string)$_POST['code']), trim((string)$_POST['name']), trim((string)$_POST['category']), trim((string)$_POST['icon']));
(new AuditRepository($smPdo))->add((int)sm_current_user()['id'], 'ppe.save', ['code' => $_POST['code'] ?? '']);
sm_redirect('/index.php?page=dashboard');
