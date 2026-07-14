<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
sm_env_load(__DIR__ . '/../../.env');
$smConfig = require __DIR__ . '/../config/app.php';

require_once __DIR__ . '/../services/Database.php';
require_once __DIR__ . '/../services/MigrationService.php';
require_once __DIR__ . '/../services/I18nService.php';
require_once __DIR__ . '/security_headers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_name($smConfig['session']['name']);
    session_set_cookie_params([
        'lifetime' => $smConfig['session']['lifetime'],
        'path' => '/',
        'httponly' => true,
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'samesite' => 'Lax',
    ]);
    session_start();
}

if (!isset($_SESSION['sm_lang'])) {
    $_SESSION['sm_lang'] = $smConfig['default_language'];
}

require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/rate_limit.php';
require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../services/RequirementInheritanceService.php';
require_once __DIR__ . '/../services/WorkflowService.php';
require_once __DIR__ . '/../controllers/PageController.php';
require_once __DIR__ . '/../repositories/BaseRepository.php';
require_once __DIR__ . '/../repositories/UserRepository.php';
require_once __DIR__ . '/../repositories/LibraryRepository.php';
require_once __DIR__ . '/../repositories/RequirementRepository.php';
require_once __DIR__ . '/../repositories/AuditRepository.php';

$smPdo = Database::connect($smConfig['db']);
MigrationService::migrate($smPdo, __DIR__ . '/../../database/migrations');
MigrationService::seed($smPdo, __DIR__ . '/../../database/seeds');
