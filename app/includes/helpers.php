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
 * Tarkistaa, onko nykyisellä kirjautuneella käyttäjällä hallintaroolit.
 * Käytetään hallintatoimintojen näyttämiseen käyttöliittymässä.
 */
function sm_is_admin(): bool
{
    $role = sm_current_user()['role_slug'] ?? '';
    return in_array($role, ['admin', 'manager', 'hseq_reviewer', 'hseq_approver', 'site_manager'], true);
}


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
    $imagePath  = (string)($ppe['image_path'] ?? '');

    // Kuva: jos ladattu kuva olemassa käytetään sitä, muuten SVG-ikoni
    if ($imagePath !== '') {
        $imgSrc = sm_h(sm_base_url()) . '/app/api/ppe_image.php?f=' . sm_h($imagePath);
    } else {
        $imgSrc = sm_h(sm_base_url()) . '/assets/img/ppe/' . $icon;
    }

    $out  = '<article class="sm-ppe-card sm-ppe-card-' . $levelClass . '">';
    $out .= '<div class="sm-ppe-card-header">';
    $out .= '<img src="' . $imgSrc . '" alt="' . $name . '" width="40" height="40" loading="lazy" class="sm-ppe-img">';
    $out .= '<div class="sm-ppe-card-title">';
    $out .= '<h3>' . $name . '</h3>';
    $out .= '<div class="sm-ppe-card-meta-row">';
    if ($stdRef !== '') {
        $out .= '<span class="sm-ppe-std">' . sm_h($stdRef) . '</span>';
    }
    $out .= '<span class="sm-badge sm-badge-' . $levelClass . '">' . $levelLabel . '</span>';
    $out .= '</div>';
    $out .= '</div>';
    $out .= '</div>';

    // Lisätiedot (ehto tai huomio) avattavana accordionina
    $detail = $condition !== '' ? $condition : $notes;
    if ($detail !== '') {
        $detailLabel = $condition !== ''
            ? '<span aria-hidden="true">⚠</span> ' . sm_h(sm_t('label_condition', $lang))
            : sm_h(sm_t('notes', $lang));
        $out .= '<details class="sm-ppe-details">';
        $out .= '<summary class="sm-ppe-details-toggle">' . $detailLabel . '</summary>';
        $out .= '<p class="sm-ppe-details-text">' . sm_h($detail) . '</p>';
        $out .= '</details>';
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

function sm_tr(string $key, array $replacements = [], string $lang = 'fi'): string
{
    $text = sm_t($key, $lang);
    foreach ($replacements as $name => $value) {
        $text = str_replace('{' . $name . '}', (string)$value, $text);
    }
    return $text;
}

/** Palauttaa PPE-kuvan URL-osoitteen (kuva tai SVG-ikoni) */
function sm_ppe_img_url(array $ppe): string
{
    $imagePath = (string)($ppe['image_path'] ?? '');
    if ($imagePath !== '') {
        return sm_base_url() . '/app/api/ppe_image.php?f=' . urlencode($imagePath);
    }
    return sm_base_url() . '/assets/img/ppe/' . ($ppe['icon'] ?? 'shield.svg');
}

/** Palauttaa tehtävän kansikuvan URL-osoitteen tai null jos kuvaa ei ole */
function sm_task_img_url(array $task): ?string
{
    $imagePath = (string)($task['cover_image_path'] ?? '');
    if ($imagePath !== '') {
        return sm_base_url() . '/app/api/task_image.php?f=' . urlencode($imagePath);
    }
    return null;
}
