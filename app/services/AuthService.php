<?php
declare(strict_types=1);

final class AuthService
{
    public function __construct(private UserRepository $users, private AuditRepository $audit, private PDO $pdo)
    {
    }

    public function login(string $email, string $password, string $ip): ?array
    {
        $limit = sm_login_allowed($this->pdo, $email, $ip);
        if (!$limit['allowed']) {
            return null;
        }

        $user = $this->users->findByEmail($email);
        $ok = $user && password_verify($password, (string)$user['password_hash']);
        sm_record_login_attempt($this->pdo, $email, $ip, (bool)$ok);

        if (!$ok) {
            return null;
        }

        $this->audit->add((int)$user['id'], 'auth.login', ['email' => $email]);

        return [
            'id' => (int)$user['id'],
            'email' => (string)$user['email'],
            'display_name' => (string)$user['display_name'],
            'role_slug' => (string)$user['role_slug'],
        ];
    }
}
