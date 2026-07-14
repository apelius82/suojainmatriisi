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
        return $this->pdo->query('SELECT id, code, name, category, item_class, standard_ref, icon, image_path FROM ppe_items WHERE is_active = 1 ORDER BY item_class, category, name')->fetchAll() ?: [];
    }

    public function findPpeItem(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, code, name, category, item_class, standard_ref, icon, image_path FROM ppe_items WHERE id = ? AND is_active = 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findPpeItemByCode(string $code): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, code, name, category, item_class, standard_ref, icon, image_path FROM ppe_items WHERE code = ? AND is_active = 1');
        $stmt->execute([$code]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findSite(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, environment_id, name, code, description FROM ppe_sites WHERE id = ? AND is_active = 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findTask(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, name, work_type, category, description FROM ppe_tasks WHERE id = ? AND is_active = 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function upsertSite(string $name, string $code, ?int $envId = null): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO ppe_sites (environment_id, name, code, is_active, created_at) VALUES (?, ?, ?, 1, NOW()) ON DUPLICATE KEY UPDATE name = VALUES(name), environment_id = VALUES(environment_id), is_active = 1');
        $stmt->execute([$envId, $name, $code]);
    }

    public function updateSite(int $id, string $name, string $code, ?int $envId = null): void
    {
        $stmt = $this->pdo->prepare('UPDATE ppe_sites SET name = ?, code = ?, environment_id = ? WHERE id = ?');
        $stmt->execute([$name, $code, $envId ?: null, $id]);
    }

    public function archiveSite(int $id): void
    {
        $stmt = $this->pdo->prepare('UPDATE ppe_sites SET is_active = 0 WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function upsertTask(string $name, string $category, string $workType = 'task'): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO ppe_tasks (name, work_type, category, is_active, created_at) VALUES (?, ?, ?, 1, NOW()) ON DUPLICATE KEY UPDATE category = VALUES(category), work_type = VALUES(work_type), is_active = 1');
        $stmt->execute([$name, $workType, $category]);
    }

    public function updateTask(int $id, string $name, string $category, string $workType = 'task'): void
    {
        $stmt = $this->pdo->prepare('UPDATE ppe_tasks SET name = ?, category = ?, work_type = ? WHERE id = ?');
        $stmt->execute([$name, $category, $workType, $id]);
    }

    public function archiveTask(int $id): void
    {
        $stmt = $this->pdo->prepare('UPDATE ppe_tasks SET is_active = 0 WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function upsertPpeItem(string $code, string $name, string $category, string $icon, string $itemClass = 'personal_protection', string $standardRef = ''): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO ppe_items (code, name, category, item_class, standard_ref, icon, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, 1, NOW()) ON DUPLICATE KEY UPDATE name = VALUES(name), category = VALUES(category), item_class = VALUES(item_class), standard_ref = VALUES(standard_ref), icon = VALUES(icon), is_active = 1');
        $stmt->execute([$code, $name, $category, $itemClass, $standardRef ?: null, $icon]);
    }

    public function updatePpeItem(int $id, string $name, string $category, string $icon, string $itemClass, string $standardRef): void
    {
        $stmt = $this->pdo->prepare('UPDATE ppe_items SET name = ?, category = ?, icon = ?, item_class = ?, standard_ref = ? WHERE id = ?');
        $stmt->execute([$name, $category, $icon, $itemClass, $standardRef ?: null, $id]);
    }

    public function updatePpeItemImage(int $id, string $imagePath): void
    {
        $stmt = $this->pdo->prepare('UPDATE ppe_items SET image_path = ? WHERE id = ?');
        $stmt->execute([$imagePath, $id]);
    }

    public function archivePpeItem(int $id): void
    {
        $stmt = $this->pdo->prepare('UPDATE ppe_items SET is_active = 0 WHERE id = ?');
        $stmt->execute([$id]);
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

    /** Tehtävät joihin tietty PPE on liitetty julkaistuilla säännöillä */
    public function tasksByPpeItem(int $ppeId): array
    {
        $sql = 'SELECT DISTINCT t.id, t.name, t.work_type, t.category FROM ppe_tasks t
                JOIN ppe_requirement_rules rr ON rr.task_id = t.id
                WHERE rr.ppe_item_id = ? AND rr.status = \'published\' AND t.is_active = 1
                ORDER BY t.name';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$ppeId]);
        return $stmt->fetchAll() ?: [];
    }

    /** Suojaimet jotka on liitetty tiettyyn tehtävään julkaistuilla säännöillä */
    public function ppeByTask(int $taskId): array
    {
        $sql = 'SELECT DISTINCT p.id, p.name, p.code, p.icon, p.item_class, p.standard_ref, p.image_path,
                       rr.requirement_level, rr.condition_text
                FROM ppe_items p
                JOIN ppe_requirement_rules rr ON rr.ppe_item_id = p.id
                WHERE rr.task_id = ? AND rr.status = \'published\' AND p.is_active = 1
                ORDER BY rr.requirement_level, p.name';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$taskId]);
        return $stmt->fetchAll() ?: [];
    }
}
