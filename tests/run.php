<?php
declare(strict_types=1);

assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_EXCEPTION, 1);

require_once __DIR__ . '/InheritanceServiceTest.php';
require_once __DIR__ . '/WorkflowAuthCsrfTest.php';
require_once __DIR__ . '/I18nSearchTest.php';

$tests = get_defined_functions()['user'];
$ran = 0;
foreach ($tests as $fn) {
    if (str_starts_with($fn, 'test_')) {
        $fn();
        $ran++;
    }
}

echo "OK: {$ran} tests passed\n";
