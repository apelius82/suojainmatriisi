<?php
declare(strict_types=1);

final class LibraryRepository extends BaseRepository
{
    public function allSites(?int $envId = null): array
    {
        if ($envId !== null) {
            $stmt = $this->pdo->prepare('SELECT id, environment_id, name, code, description FROM ppe_sites WHERE is_active = 1 AND environment_id = ? ORDER BY name');
            $stmt->execute([$envId]);
            return $stmt->fetchAll() ?: [];
        }
        return $this->pdo->query('SELECT id, environment_id, name, code, description FROM ppe_sites WHERE is_active = 1 ORDER BY name')->fetchAll() ?: [];
    }

    public function allTasks(?int $envId = null): array
    {
        if ($envId !== null) {
            $sql = 'SELECT DISTINCT t.id, t.name, t.work_type, t.category, t.description
                    FROM ppe_tasks t
                    INNER JOIN ppe_requirement_rules rr ON rr.task_id = t.id AND rr.environment_id = ? AND rr.status = \'published\'
                    WHERE t.is_active = 1 ORDER BY t.name';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$envId]);
            $rows = $stmt->fetchAll() ?: [];
            if (!empty($rows)) {
                return $rows;
            }
        }
        return $this->pdo->query('SELECT id, name, work_type, category, description FROM ppe_tasks WHERE is_active = 1 ORDER BY name')->fetchAll() ?: [];
    }

    public function allPpeItems(): array
    {
        return $this->pdo->query('SELECT id, code, name, category, item_class, standard_ref, icon FROM ppe_items WHERE is_active = 1 ORDER BY item_class, category, name')->fetchAll() ?: [];
    }

    public function upsertSite(string $name, string $code, ?int $envId = null): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO ppe_sites (environment_id, name, code, is_active, created_at) VALUES (?, ?, ?, 1, NOW()) ON DUPLICATE KEY UPDATE name = VALUES(name), environment_id = VALUES(environment_id), is_active = 1');
        $stmt->execute([$envId, $name, $code]);
    }

    public function upsertTask(string $name, string $category, string $workType = 'task'): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO ppe_tasks (name, work_type, category, is_active, created_at) VALUES (?, ?, ?, 1, NOW()) ON DUPLICATE KEY UPDATE category = VALUES(category), work_type = VALUES(work_type), is_active = 1');
        $stmt->execute([$name, $workType, $category]);
    }

    public function upsertPpeItem(string $code, string $name, string $category, string $icon, string $itemClass = 'personal_protection', string $standardRef = ''): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO ppe_items (code, name, category, item_class, standard_ref, icon, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, 1, NOW()) ON DUPLICATE KEY UPDATE name = VALUES(name), category = VALUES(category), item_class = VALUES(item_class), standard_ref = VALUES(standard_ref), icon = VALUES(icon), is_active = 1');
        $stmt->execute([$code, $name, $category, $itemClass, $standardRef ?: null, $icon]);
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
