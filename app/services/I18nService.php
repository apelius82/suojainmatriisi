<?php
declare(strict_types=1);

final class I18nService
{
    public static function config(): array
    {
        return require __DIR__ . '/../config/terms/_index.php';
    }

    public static function t(string $key, ?string $lang = null): string
    {
        global $smConfig;
        $cfg = self::config();
        $lang ??= $_SESSION['sm_lang'] ?? $smConfig['default_language'] ?? 'fi';
        $lang = in_array($lang, $cfg['languages'], true) ? $lang : 'fi';
        return $cfg['terms'][$key][$lang] ?? $cfg['terms'][$key]['fi'] ?? $key;
    }
}

function sm_t(string $key, ?string $lang = null): string
{
    return I18nService::t($key, $lang);
}
