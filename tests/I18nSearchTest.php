<?php
declare(strict_types=1);

function test_supported_languages_and_terms(): void
{
    $cfg = require __DIR__ . '/../app/config/terms/_index.php';
    assert($cfg['languages'] === ['fi', 'sv', 'en', 'it', 'el']);
    assert(isset($cfg['terms']['app_title']['en']));
}
