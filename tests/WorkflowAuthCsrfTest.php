<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/services/WorkflowService.php';
require_once __DIR__ . '/../app/includes/csrf.php';
require_once __DIR__ . '/../app/includes/auth.php';

function test_workflow_transitions(): void
{
    $wf = new WorkflowService();
    assert($wf->canTransition('draft', 'review') === true);
    assert($wf->canTransition('published', 'draft') === false);
}

function test_csrf_and_auth(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $token = sm_csrf_token();
    assert(sm_csrf_validate($token) === true);
    assert(sm_csrf_validate('bad') === false);

    sm_auth_login(['id' => 1, 'role_slug' => 'admin']);
    assert(sm_current_user() !== null);
}
