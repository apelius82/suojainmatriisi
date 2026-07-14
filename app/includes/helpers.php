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
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0775, true);
    }
    $line = sprintf("[%s] %s %s\n", date('c'), $message, $context ? json_encode($context, JSON_UNESCAPED_UNICODE) : '');
    $result = file_put_contents($logFile, $line, FILE_APPEND);
    if ($result === false) {
        error_log('sm_log write failed for ' . $logFile);
    }
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

/**
 * Renderöi PPE-kortti tulossivulle.
 */
function sm_render_ppe_card(array $card, string $lang = 'fi'): string
{
    $rule       = $card['rule'];
    $ppe        = $card['ppe'];
    $level      = (string)$rule['requirement_level'];
    $notes      = (string)($rule['notes'] ?? '');
    $condition  = (string)($rule['condition_text'] ?? '');
    $scope      = (string)($rule['_source_scope'] ?? $rule['scope_type'] ?? '');
    $stdRef     = (string)($ppe['standard_ref'] ?? '');
    $icon       = sm_h((string)$ppe['icon']);
    $name       = sm_h((string)$ppe['name']);
    $levelLabel = sm_h(sm_t($level, $lang));
    $levelClass = sm_h($level);

    $out  = '<article class="sm-ppe-card sm-ppe-card-' . $levelClass . '">';
    $out .= '<div class="sm-ppe-card-header">';
    $out .= '<img src="' . sm_h(sm_base_url()) . '/assets/img/ppe/' . $icon . '" alt="" width="44" height="44" loading="lazy">';
    $out .= '<div class="sm-ppe-card-title">';
    $out .= '<h3>' . $name . '</h3>';
    if ($stdRef !== '') {
        $out .= '<span class="sm-ppe-std">' . sm_h($stdRef) . '</span>';
    }
    $out .= '</div>';
    $out .= '</div>';
    $out .= '<div class="sm-ppe-card-footer">';
    $out .= '<span class="sm-badge sm-badge-' . $levelClass . '">' . $levelLabel . '</span>';
    if ($scope !== '') {
        $scopeLabel = '';
        if (function_exists('sm_scope_label')) {
            $scopeLabel = sm_scope_label($scope, $lang);
        }
        if ($scopeLabel !== '') {
            $out .= '<span class="sm-ppe-scope">' . sm_h($scopeLabel) . '</span>';
        }
    }
    $out .= '</div>';
    if ($condition !== '') {
        $out .= '<p class="sm-ppe-condition"><strong>' . sm_h(sm_t('condition', $lang)) . ':</strong> ' . sm_h($condition) . '</p>';
    } elseif ($notes !== '') {
        $out .= '<p class="sm-ppe-condition">' . sm_h($notes) . '</p>';
    }
    $out .= '</article>';
    return $out;
}

function sm_scope_label(string $scope, string $lang = 'fi'): string
{
    $key = 'scope_' . $scope;
    $val = sm_t($key, $lang);
    return $val !== $key ? $val : $scope;
}
