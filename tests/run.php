<?php
declare(strict_types=1);

assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_EXCEPTION, 1);

require_once __DIR__ . '/InheritanceServiceTest.php';
require_once __DIR__ . '/WorkflowAuthCsrfTest.php';
require_once __DIR__ . '/I18nSearchTest.php';
require_once __DIR__ . '/RequirementResolverTest.php';

$ran = 0;
foreach ([
    'test_inheritance_resolution',
    'test_workflow_transitions',
    'test_csrf_and_auth',
    'test_supported_languages_and_terms',
    'test_global_requirement_inherited',
    'test_site_overrides_global',
    'test_zone_overrides_site',
    'test_conditional_activates_with_task',
    'test_draft_not_visible',
    'test_environment_scope_filtered',
    'test_avolouhos_poraus_scenario',
    'test_stricter_level_wins_across_scopes',
    'test_scope_level_priority_order',
    'test_group_sections',
] as $fn) {
    $fn();
    $ran++;
}

echo "OK: {$ran} tests passed\n";
