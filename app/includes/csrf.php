<?php
declare(strict_types=1);

function sm_csrf_token(): string
{
    if (empty($_SESSION['sm_csrf'])) {
        $_SESSION['sm_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['sm_csrf'];
}

function sm_csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . sm_h(sm_csrf_token()) . '">';
}

function sm_csrf_validate(?string $token = null): bool
{
    $token ??= $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['csrf_token'] ?? '';
    return !empty($_SESSION['sm_csrf']) && !empty($token) && hash_equals((string)$_SESSION['sm_csrf'], $token);
}

function sm_csrf_require(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && !sm_csrf_validate()) {
        http_response_code(403);
        exit('CSRF validation failed');
    }
}
