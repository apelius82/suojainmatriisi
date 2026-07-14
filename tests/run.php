<?php
declare(strict_types=1);

assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_EXCEPTION, 1);

require_once __DIR__ . '/InheritanceServiceTest.php';
require_once __DIR__ . '/WorkflowAuthCsrfTest.php';
require_once __DIR__ . '/I18nSearchTest.php';

$ran = 0;
foreach ([
    'test_inheritance_resolution',
    'test_workflow_transitions',
    'test_csrf_and_auth',
    'test_languages_available',
] as $fn) {
    $fn();
    $ran++;
}

echo "OK: {$ran} tests passed\n";
