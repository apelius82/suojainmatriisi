<?php
declare(strict_types=1);

final class RequirementRepository extends BaseRepository
{
    public function listRules(?string $status = 'published'): array
    {
        $sql = 'SELECT rr.id, rr.scope_type, rr.site_id, rr.task_id, rr.ppe_item_id, rr.requirement_level, rr.status, rr.version_no, rr.notes, s.name AS site_name, t.name AS task_name, p.name AS ppe_name
                FROM ppe_requirement_rules rr
                LEFT JOIN ppe_sites s ON s.id = rr.site_id
                LEFT JOIN ppe_tasks t ON t.id = rr.task_id
                JOIN ppe_items p ON p.id = rr.ppe_item_id';
        $params = [];
        if ($status !== null) {
            $sql .= ' WHERE rr.status = ?';
            $params[] = $status;
        }
        $sql .= ' ORDER BY rr.scope_type, s.name, t.name, p.name';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }

    public function addRule(array $rule, int $userId): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO ppe_requirement_rules (scope_type, site_id, task_id, ppe_item_id, requirement_level, status, version_no, notes, created_by, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
        $stmt->execute([
            $rule['scope_type'],
            $rule['site_id'] ?: null,
            $rule['task_id'] ?: null,
            $rule['ppe_item_id'],
            $rule['requirement_level'],
            $rule['status'],
            1,
            $rule['notes'] ?? null,
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
