<?php
declare(strict_types=1);

function test_supported_languages_and_terms(): void
{
    $cfg = require __DIR__ . '/../app/config/terms/_index.php';
    assert($cfg['languages'] === ['fi', 'sv', 'en', 'it', 'el']);
    assert(isset($cfg['terms']['app_title']['en']));

    // New terms from admin.php module
    $langs = ['fi', 'sv', 'en', 'it', 'el'];
    $requiredNewTerms = [
        'environment', 'environments', 'zone', 'zones',
        'mandatory', 'conditional', 'prohibited', 'information', 'not_applicable',
        'personal_protection', 'other_safety',
        'section_always', 'section_conditional', 'section_other', 'section_critical', 'section_attachments',
        'official_only', 'select_environment', 'select_site', 'select_zone', 'select_task',
    ];
    foreach ($requiredNewTerms as $key) {
        assert(isset($cfg['terms'][$key]), "Term '$key' missing");
        foreach ($langs as $lang) {
            assert(isset($cfg['terms'][$key][$lang]) && $cfg['terms'][$key][$lang] !== '', "Term '$key' missing lang '$lang'");
        }
    }
}
