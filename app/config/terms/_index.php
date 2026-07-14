<?php
$modules = [
    require __DIR__ . '/common.php',
    require __DIR__ . '/navigation.php',
    require __DIR__ . '/ppe.php',
    require __DIR__ . '/admin.php',
];
$terms = [];
foreach ($modules as $module) {
    $terms = array_merge($terms, $module);
}
return [
    'languages' => ['fi', 'sv', 'en', 'it', 'el'],
    'terms' => $terms,
];
