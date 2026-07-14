<?php
declare(strict_types=1);

final class LibraryRepository extends BaseRepository
{
    public function allSites(): array
    {
        return $this->pdo->query('SELECT id, name, code FROM ppe_sites WHERE is_active = 1 ORDER BY name')->fetchAll() ?: [];
    }

    public function allTasks(): array
    {
        return $this->pdo->query('SELECT id, name, category FROM ppe_tasks WHERE is_active = 1 ORDER BY name')->fetchAll() ?: [];
    }

    public function allPpeItems(): array
    {
        return $this->pdo->query('SELECT id, code, name, category, icon FROM ppe_items WHERE is_active = 1 ORDER BY category, name')->fetchAll() ?: [];
    }

    public function upsertSite(string $name, string $code): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO ppe_sites (name, code, is_active, created_at) VALUES (?, ?, 1, NOW()) ON DUPLICATE KEY UPDATE name = VALUES(name), is_active = 1');
        $stmt->execute([$name, $code]);
    }

    public function upsertTask(string $name, string $category): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO ppe_tasks (name, category, is_active, created_at) VALUES (?, ?, 1, NOW()) ON DUPLICATE KEY UPDATE category = VALUES(category), is_active = 1');
        $stmt->execute([$name, $category]);
    }

    public function upsertPpeItem(string $code, string $name, string $category, string $icon): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO ppe_items (code, name, category, icon, is_active, created_at) VALUES (?, ?, ?, ?, 1, NOW()) ON DUPLICATE KEY UPDATE name = VALUES(name), category = VALUES(category), icon = VALUES(icon), is_active = 1');
        $stmt->execute([$code, $name, $category, $icon]);
    }

    public function allWorkers(): array
    {
        $sql = 'SELECT w.id, w.full_name, w.site_id, w.task_id, s.name AS site_name, t.name AS task_name FROM ppe_workers w JOIN ppe_sites s ON s.id = w.site_id JOIN ppe_tasks t ON t.id = w.task_id WHERE w.is_active = 1 ORDER BY w.full_name';
        return $this->pdo->query($sql)->fetchAll() ?: [];
    }

    public function upsertWorker(string $fullName, int $siteId, int $taskId): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO ppe_workers (full_name, site_id, task_id, is_active, created_at) VALUES (?, ?, ?, 1, NOW()) ON DUPLICATE KEY UPDATE site_id = VALUES(site_id), task_id = VALUES(task_id), is_active = 1');
        $stmt->execute([$fullName, $siteId, $taskId]);
    }
}
