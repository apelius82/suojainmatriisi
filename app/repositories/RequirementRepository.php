<?php
declare(strict_types=1);

final class RequirementRepository extends BaseRepository
{
    public function listRules(?string $status = 'published', ?int $envId = null): array
    {
        $sql = 'SELECT rr.id, rr.scope_type, rr.environment_id, rr.site_id, rr.zone_id, rr.task_id,
                       rr.ppe_item_id, rr.requirement_level, rr.status, rr.version_no, rr.notes,
                       rr.condition_text, rr.effective_date, rr.change_description,
                       e.name AS env_name, s.name AS site_name, z.name AS zone_name,
                       t.name AS task_name, p.name AS ppe_name, p.item_class AS ppe_class
                FROM ppe_requirement_rules rr
                LEFT JOIN ppe_environments e ON e.id = rr.environment_id
                LEFT JOIN ppe_sites s ON s.id = rr.site_id
                LEFT JOIN ppe_zones z ON z.id = rr.zone_id
                LEFT JOIN ppe_tasks t ON t.id = rr.task_id
                JOIN ppe_items p ON p.id = rr.ppe_item_id';
        $params = [];
        $where  = [];
        if ($status !== null) {
            $where[]  = 'rr.status = ?';
            $params[] = $status;
        }
        if ($envId !== null) {
            $where[]  = 'rr.environment_id = ?';
            $params[] = $envId;
        }
        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY rr.scope_type, e.name, s.name, z.name, t.name, p.name';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }

    public function addRule(array $rule, int $userId): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO ppe_requirement_rules
               (scope_type, environment_id, site_id, zone_id, task_id,
                ppe_item_id, requirement_level, status, version_no, notes, condition_text,
                change_description, created_by, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())'
        );
        $stmt->execute([
            $rule['scope_type'],
            $rule['environment_id'] ?: null,
            $rule['site_id'] ?: null,
            $rule['zone_id'] ?: null,
            $rule['task_id'] ?: null,
            $rule['ppe_item_id'],
            $rule['requirement_level'],
            $rule['status'],
            1,
            $rule['notes'] ?? null,
            $rule['condition_text'] ?? null,
            $rule['change_description'] ?? null,
            $userId,
        ]);

        $id = (int)$this->pdo->lastInsertId();
        $this->saveVersion($id, 1, $rule['status'], $rule['requirement_level'], $userId, $rule['notes'] ?? null);
        return $id;
    }

    public function changeStatus(int $ruleId, string $status, int $userId): void
    {
        $stmt = $this->pdo->prepare('UPDATE ppe_requirement_rules SET status = ?, version_no = version_no + 1, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$status, $ruleId]);

        $rule = $this->findRule($ruleId);
        if ($rule) {
            $this->saveVersion($ruleId, (int)$rule['version_no'], $status, $rule['requirement_level'], $userId, 'status_change');
        }
    }

    public function updateRule(int $id, array $rule, int $userId): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE ppe_requirement_rules SET
               scope_type = ?, environment_id = ?, site_id = ?, zone_id = ?, task_id = ?,
               ppe_item_id = ?, requirement_level = ?, notes = ?, condition_text = ?,
               change_description = ?, version_no = version_no + 1, updated_at = NOW()
             WHERE id = ?'
        );
        $stmt->execute([
            $rule['scope_type'],
            $rule['environment_id'] ?: null,
            $rule['site_id'] ?: null,
            $rule['zone_id'] ?: null,
            $rule['task_id'] ?: null,
            $rule['ppe_item_id'],
            $rule['requirement_level'],
            $rule['notes'] ?? null,
            $rule['condition_text'] ?? null,
            $rule['change_description'] ?? null,
            $id,
        ]);
        $updated = $this->findRule($id);
        if ($updated) {
            $this->saveVersion($id, (int)$updated['version_no'], $updated['status'], $updated['requirement_level'], $userId, $rule['change_description'] ?? null);
        }
    }

    public function archiveRule(int $ruleId, int $userId): void
    {
        $this->changeStatus($ruleId, 'archived', $userId);
    }

    /** Massalisäys: lisää sama vaatimus usealle tehtävälle samassa työmaa/alue/ympäristö-hakuehdossa */
    public function bulkAddRule(array $baseRule, array $taskIds, int $userId): int
    {
        $count = 0;
        foreach ($taskIds as $taskId) {
            $rule = $baseRule;
            $rule['task_id']    = (int)$taskId;
            $rule['scope_type'] = self::deriveScopeType((int)$baseRule['site_id'], (int)$baseRule['environment_id']);
            $this->addRule($rule, $userId);
            $count++;
        }
        return $count;
    }

    /** Johtaa scope_type-arvon työmaa- ja ympäristötunnisteiden perusteella. */
    public static function deriveScopeType(int $siteId, int $envId): string
    {
        if ($siteId > 0) {
            return 'site_task';
        }
        if ($envId > 0) {
            return 'task';
        }
        return 'global';
    }

    public function findRule(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM ppe_requirement_rules WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    private function saveVersion(int $ruleId, int $versionNo, string $status, string $level, int $userId, ?string $notes): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO ppe_rule_versions (rule_id, version_no, status, requirement_level, notes, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute([$ruleId, $versionNo, $status, $level, $notes, $userId]);
    }

    public function versionHistory(int $ruleId): array
    {
        $stmt = $this->pdo->prepare('SELECT version_no, status, requirement_level, notes, created_at FROM ppe_rule_versions WHERE rule_id = ? ORDER BY version_no DESC');
        $stmt->execute([$ruleId]);
        return $stmt->fetchAll() ?: [];
    }
}
