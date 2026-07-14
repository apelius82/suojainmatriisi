<?php
declare(strict_types=1);

function sm_env_load(string $path): void
{
    if (!is_file($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        if (getenv($key) === false) {
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
        }
    }
}

function sm_log(string $message, array $context = []): void
{
    global $smConfig;
    $logFile = $smConfig['log_file'] ?? (__DIR__ . '/../../storage/logs/suojainmatriisi.log');
    $line = sprintf("[%s] %s %s\n", date('c'), $message, $context ? json_encode($context, JSON_UNESCAPED_UNICODE) : '');
    @file_put_contents($logFile, $line, FILE_APPEND);
}

function sm_base_url(): string
{
    global $smConfig;
    return $smConfig['base_url'] ?? '';
}

function sm_redirect(string $path): never
{
    $base = sm_base_url();
    header('Location: ' . $base . $path);
    exit;
}

function sm_h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
