<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/services/RequirementInheritanceService.php';

function test_inheritance_resolution(): void
{
    $svc = new RequirementInheritanceService();
    $rules = [
        ['scope_type' => 'global', 'site_id' => null, 'task_id' => null, 'ppe_item_id' => 1, 'requirement_level' => 'required', 'status' => 'published'],
        ['scope_type' => 'task', 'site_id' => null, 'task_id' => 2, 'ppe_item_id' => 1, 'requirement_level' => 'forbidden', 'status' => 'published'],
        ['scope_type' => 'local', 'site_id' => 1, 'task_id' => 2, 'ppe_item_id' => 2, 'requirement_level' => 'required', 'status' => 'published'],
    ];
    $result = $svc->resolve($rules, 1, 2);
    assert(count($result['resolved']) === 2);
    assert(count($result['conflicts']) === 1);
}
