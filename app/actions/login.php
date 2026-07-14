<?php
declare(strict_types=1);

sm_csrf_require();

$email = trim((string)($_POST['email'] ?? ''));
$password = (string)($_POST['password'] ?? '');
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

$authService = new AuthService(new UserRepository($smPdo), new AuditRepository($smPdo), $smPdo);
$user = $authService->login($email, $password, $ip);

if (!$user) {
    $_SESSION['sm_flash_error'] = 'Virheellinen tunnus tai salasana.';
    sm_redirect('/index.php?page=login');
}

sm_auth_login($user);
sm_redirect('/index.php?page=dashboard');
