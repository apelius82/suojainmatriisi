<?php
declare(strict_types=1);

function sm_current_user(): ?array
{
    return $_SESSION['sm_user'] ?? null;
}

function sm_auth_login(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['sm_user'] = $user;
}

function sm_auth_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $params['path'], $params['domain'], (bool)$params['secure'], (bool)$params['httponly']);
    }
    session_destroy();
}

function sm_require_login(): void
{
    if (!sm_current_user()) {
        sm_redirect('/index.php?page=login');
    }
}

function sm_require_role(array $roles): void
{
    $user = sm_current_user();
    if (!$user || !in_array($user['role_slug'] ?? '', $roles, true)) {
        http_response_code(403);
        exit('Ei käyttöoikeutta.');
    }
}
