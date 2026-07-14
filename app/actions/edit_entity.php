<?php
declare(strict_types=1);
// Muokkaa olemassa olevaa entiteettiä
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
        (new LibraryRepository($smPdo))->updateSite(
            $id,
            trim((string)$_POST['name']),
            trim((string)$_POST['code']),
            (int)($_POST['environment_id'] ?? 0) ?: null
        );
        $audit->add((int)$user['id'], 'site.edit', ['id' => $id]);
        sm_redirect('/index.php?page=dashboard&tab=sites');
        break;
    case 'task':
        (new LibraryRepository($smPdo))->updateTask(
            $id,
            trim((string)$_POST['name']),
            trim((string)$_POST['category']),
            trim((string)($_POST['work_type'] ?? 'task'))
        );
        $audit->add((int)$user['id'], 'task.edit', ['id' => $id]);
        sm_redirect('/index.php?page=dashboard&tab=tasks');
        break;
    case 'ppe':
        (new LibraryRepository($smPdo))->updatePpeItem(
            $id,
            trim((string)$_POST['name']),
            trim((string)$_POST['category']),
            trim((string)$_POST['icon']),
            trim((string)($_POST['item_class'] ?? 'personal_protection')),
            trim((string)($_POST['standard_ref'] ?? ''))
        );
        $audit->add((int)$user['id'], 'ppe.edit', ['id' => $id]);
        sm_redirect('/index.php?page=dashboard&tab=ppe');
        break;
    case 'environment':
        (new EnvironmentRepository($smPdo))->update(
            $id,
            trim((string)$_POST['code']),
            trim((string)$_POST['name']),
            trim((string)($_POST['description'] ?? ''))
        );
        $audit->add((int)$user['id'], 'environment.edit', ['id' => $id]);
        sm_redirect('/index.php?page=dashboard&tab=environments');
        break;
    case 'zone':
        (new ZoneRepository($smPdo))->update(
            $id,
            (int)($_POST['site_id'] ?? 0),
            trim((string)$_POST['code']),
            trim((string)$_POST['name']),
            trim((string)($_POST['description'] ?? ''))
        );
        $audit->add((int)$user['id'], 'zone.edit', ['id' => $id]);
        sm_redirect('/index.php?page=dashboard&tab=zones');
        break;
    default:
        sm_redirect('/index.php?page=dashboard');
}
