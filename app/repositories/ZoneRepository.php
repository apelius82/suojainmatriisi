<?php
declare(strict_types=1);

final class ZoneRepository extends BaseRepository
{
    public function allBySite(int $siteId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, site_id, parent_zone_id, code, name, description
             FROM ppe_zones WHERE site_id = ? AND is_active = 1 ORDER BY name'
        );
        $stmt->execute([$siteId]);
        return $stmt->fetchAll() ?: [];
    }

    public function all(): array
    {
        $sql = 'SELECT z.id, z.site_id, z.parent_zone_id, z.code, z.name, z.description, s.name AS site_name
                FROM ppe_zones z LEFT JOIN ppe_sites s ON s.id = z.site_id
                WHERE z.is_active = 1 ORDER BY s.name, z.name';
        return $this->pdo->query($sql)->fetchAll() ?: [];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM ppe_zones WHERE id = ? AND is_active = 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function upsert(int $siteId, string $code, string $name, string $description = '', ?int $parentId = null): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO ppe_zones (site_id, parent_zone_id, code, name, description, is_active, created_at)
             VALUES (?, ?, ?, ?, ?, 1, NOW())
             ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description), is_active = 1'
        );
        $stmt->execute([$siteId, $parentId, $code, $name, $description]);
    }

    public function update(int $id, int $siteId, string $code, string $name, string $description = ''): void
    {
        $stmt = $this->pdo->prepare('UPDATE ppe_zones SET site_id = ?, code = ?, name = ?, description = ? WHERE id = ?');
        $stmt->execute([$siteId, $code, $name, $description, $id]);
    }

    public function archive(int $id): void
    {
        $stmt = $this->pdo->prepare('UPDATE ppe_zones SET is_active = 0 WHERE id = ?');
        $stmt->execute([$id]);
    }
}
