<?php
declare(strict_types=1);

final class UserRepository extends BaseRepository
{
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT u.id, u.email, u.password_hash, u.display_name, r.slug AS role_slug FROM ppe_users u JOIN ppe_roles r ON r.id = u.role_id WHERE u.email = ? AND u.is_active = 1 LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function all(): array
    {
        return $this->pdo->query('SELECT u.id, u.display_name, u.email, r.slug AS role_slug FROM ppe_users u JOIN ppe_roles r ON r.id = u.role_id ORDER BY u.display_name')->fetchAll() ?: [];
    }
}
