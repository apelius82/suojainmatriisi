<?php
declare(strict_types=1);

function sm_login_allowed(PDO $pdo, string $email, string $ip): array
{
    $stmt = $pdo->prepare('SELECT COUNT(*) AS attempts, MAX(attempted_at) AS latest FROM ppe_login_attempts WHERE success = 0 AND attempted_at > (NOW() - INTERVAL 15 MINUTE) AND (email = ? OR ip_address = ?)');
    $stmt->execute([$email, $ip]);
    $row = $stmt->fetch() ?: ['attempts' => 0, 'latest' => null];
    $attempts = (int)$row['attempts'];
    if ($attempts >= 5) {
        return ['allowed' => false, 'remaining' => 0];
    }
    return ['allowed' => true, 'remaining' => max(0, 5 - $attempts)];
}

function sm_record_login_attempt(PDO $pdo, string $email, string $ip, bool $success): void
{
    $stmt = $pdo->prepare('INSERT INTO ppe_login_attempts (email, ip_address, success, attempted_at) VALUES (?, ?, ?, NOW())');
    $stmt->execute([$email, $ip, $success ? 1 : 0]);
}
