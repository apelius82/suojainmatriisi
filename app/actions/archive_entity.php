<?php
declare(strict_types=1);
// Arkistoi entiteetti (site/task/ppe/environment/zone) pehmeällä poistolla
sm_require_role(['admin', 'manager', 'site_manager']);
sm_csrf_require();

$type = (string)($_POST['entity_type'] ?? '');
$id   = (int)($_POST['entity_id'] ?? 0);
if ($id < 1 || !in_array($type, ['site','task','ppe','environment','zone'], true)) {
    sm_redirect('/index.php?page=dashboard');
}

$audit = new AuditRepository($smPdo);
$user  = sm_current_user();

switch ($type) {
    case 'site':
        (new LibraryRepository($smPdo))->archiveSite($id);
        $audit->add((int)$user['id'], 'site.archive', ['id' => $id]);
        sm_redirect('/index.php?page=dashboard&tab=sites');
        break;
    case 'task':
        (new LibraryRepository($smPdo))->archiveTask($id);
        $audit->add((int)$user['id'], 'task.archive', ['id' => $id]);
        sm_redirect('/index.php?page=dashboard&tab=tasks');
        break;
    case 'ppe':
        (new LibraryRepository($smPdo))->archivePpeItem($id);
        $audit->add((int)$user['id'], 'ppe.archive', ['id' => $id]);
        sm_redirect('/index.php?page=dashboard&tab=ppe');
        break;
    case 'environment':
        (new EnvironmentRepository($smPdo))->archive($id);
        $audit->add((int)$user['id'], 'environment.archive', ['id' => $id]);
        sm_redirect('/index.php?page=dashboard&tab=environments');
        break;
    case 'zone':
        (new ZoneRepository($smPdo))->archive($id);
        $audit->add((int)$user['id'], 'zone.archive', ['id' => $id]);
        sm_redirect('/index.php?page=dashboard&tab=zones');
        break;
    default:
        sm_redirect('/index.php?page=dashboard');
}
