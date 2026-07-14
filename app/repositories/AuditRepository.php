<?php
declare(strict_types=1);

final class AuditRepository extends BaseRepository
{
    public function add(int $userId, string $eventType, array $payload): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO ppe_audit_log (user_id, event_type, payload_json, created_at) VALUES (?, ?, ?, NOW())');
        $stmt->execute([$userId, $eventType, json_encode($payload, JSON_UNESCAPED_UNICODE)]);
    }

    public function latest(int $limit = 100): array
    {
        $stmt = $this->pdo->prepare('SELECT a.id, a.event_type, a.payload_json, a.created_at, u.display_name FROM ppe_audit_log a LEFT JOIN ppe_users u ON u.id = a.user_id ORDER BY a.id DESC LIMIT ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }
}
