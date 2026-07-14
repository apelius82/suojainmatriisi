<?php
declare(strict_types=1);

final class EnvironmentRepository extends BaseRepository
{
    public function all(): array
    {
        return $this->pdo->query('SELECT id, code, name, description FROM ppe_environments WHERE is_active = 1 ORDER BY name')->fetchAll() ?: [];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, code, name, description FROM ppe_environments WHERE id = ? AND is_active = 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function upsert(string $code, string $name, string $description = ''): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO ppe_environments (code, name, description, is_active, created_at) VALUES (?, ?, ?, 1, NOW())
             ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description), is_active = 1'
        );
        $stmt->execute([$code, $name, $description]);
    }
}
