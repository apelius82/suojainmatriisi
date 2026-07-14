<?php
declare(strict_types=1);

require_once __DIR__ . '/app/includes/bootstrap.php';

$action = $_GET['action'] ?? '';
if ($action !== '') {
    $file = __DIR__ . '/app/actions/' . basename((string)$action) . '.php';
    if (is_file($file)) {
        require $file;
        exit;
    }
}

$page = (string)($_GET['page'] ?? 'dashboard');
if ($page === 'login') {
    ?><!DOCTYPE html>
    <html lang="<?= sm_h($_SESSION['sm_lang'] ?? 'fi') ?>">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
      <meta name="theme-color" content="#0f172a">
      <link rel="manifest" href="<?= sm_h(sm_base_url()) ?>/manifest.php">
      <link rel="stylesheet" href="<?= sm_h(sm_base_url()) ?>/assets/css/nav.css">
      <link rel="stylesheet" href="<?= sm_h(sm_base_url()) ?>/assets/css/global.css">
      <link rel="stylesheet" href="<?= sm_h(sm_base_url()) ?>/assets/css/layout.css">
      <title><?= sm_h(sm_t('app_title')) ?></title>
    </head>
    <body>
    <?php include __DIR__ . '/app/views/pages/login.php'; ?>
    </body>
    </html><?php
    exit;
}

sm_require_login();

$library      = new LibraryRepository($smPdo);
$envRepo      = new EnvironmentRepository($smPdo);
$zoneRepo     = new ZoneRepository($smPdo);
$controller   = new PageController(
    $library,
    new RequirementRepository($smPdo),
    new AuditRepository($smPdo),
    new RequirementResolver(),
    $envRepo,
    $zoneRepo
);

?><!DOCTYPE html>
<html lang="<?= sm_h($_SESSION['sm_lang'] ?? 'fi') ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <meta name="theme-color" content="#0f172a">
  <link rel="manifest" href="<?= sm_h(sm_base_url()) ?>/manifest.php">
  <link rel="stylesheet" href="<?= sm_h(sm_base_url()) ?>/assets/css/nav.css">
  <link rel="stylesheet" href="<?= sm_h(sm_base_url()) ?>/assets/css/global.css">
  <link rel="stylesheet" href="<?= sm_h(sm_base_url()) ?>/assets/css/layout.css">
  <link rel="stylesheet" href="<?= sm_h(sm_base_url()) ?>/assets/css/settings.css">
  <title><?= sm_h(sm_t('app_title')) ?></title>
</head>
<body>
<?php include __DIR__ . '/app/views/layout/header.php'; ?>
<main class="sm-page-container">
<?php
if ($page === 'search') {
    $envId  = (int)($_GET['env_id']  ?? 0);
    $siteId = (int)($_GET['site_id'] ?? 0);
    $zoneId = (int)($_GET['zone_id'] ?? 0);
    $taskId = (int)($_GET['task_id'] ?? 0);

    $searchData = $controller->searchData();
    $environments = $searchData['environments'];
    $sites        = $library->allSites($envId > 0 ? $envId : null);
    $zones        = $siteId > 0 ? $controller->zonesBySite($siteId) : [];
    $tasks        = $library->allTasks($envId > 0 ? $envId : null);

    $taskCards = [];
    if ($siteId > 0 || $envId > 0) {
        $taskCards = $controller->taskCards(
            $envId  > 0 ? $envId  : null,
            $siteId > 0 ? $siteId : null,
            $zoneId > 0 ? $zoneId : null
        );
    }
    include __DIR__ . '/app/views/pages/search.php';
} else {
    $data = $controller->dashboard();
    include __DIR__ . '/app/views/pages/dashboard.php';
}
?>
</main>
<?php include __DIR__ . '/app/views/layout/footer.php'; ?>
<script>
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('<?= sm_h(sm_base_url()) ?>/service-worker.js');
}
</script>
<script src="<?= sm_h(sm_base_url()) ?>/assets/js/modules/nav.js"></script>
</body>
</html>
