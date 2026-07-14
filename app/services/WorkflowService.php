<?php
declare(strict_types=1);

final class WorkflowService
{
    private const MAP = [
        'draft' => ['review'],
        'review' => ['approved', 'draft'],
        'approved' => ['published', 'draft'],
        'published' => ['archived'],
        'archived' => [],
    ];

    public function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::MAP[$from] ?? [], true);
    }
}
