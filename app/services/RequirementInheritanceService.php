<?php
declare(strict_types=1);

final class RequirementInheritanceService
{
    /**
     * @param array<int,array<string,mixed>> $rules
     * @return array{resolved: array<int,array<string,mixed>>, conflicts: array<int,array<string,mixed>>}
     */
    public function resolve(array $rules, ?int $siteId, ?int $taskId): array
    {
        $eligible = array_values(array_filter($rules, function (array $rule) use ($siteId, $taskId): bool {
            if (($rule['status'] ?? '') !== 'published') {
                return false;
            }
            return match ($rule['scope_type']) {
                'global' => true,
                'site' => (int)$rule['site_id'] === (int)$siteId,
                'task' => (int)$rule['task_id'] === (int)$taskId,
                'local' => (int)$rule['site_id'] === (int)$siteId && (int)$rule['task_id'] === (int)$taskId,
                default => false,
            };
        }));

        usort($eligible, fn(array $a, array $b): int => $this->priority($a['scope_type']) <=> $this->priority($b['scope_type']));

        $resolved = [];
        $conflicts = [];

        foreach ($eligible as $rule) {
            $ppeId = (int)$rule['ppe_item_id'];
            if (!isset($resolved[$ppeId])) {
                $resolved[$ppeId] = $rule;
                continue;
            }
            if (($resolved[$ppeId]['requirement_level'] ?? '') !== ($rule['requirement_level'] ?? '')) {
                $conflicts[] = [
                    'ppe_item_id' => $ppeId,
                    'kept' => $resolved[$ppeId],
                    'discarded' => $rule,
                ];
            }
        }

        return ['resolved' => array_values($resolved), 'conflicts' => $conflicts];
    }

    private function priority(string $scope): int
    {
        return match ($scope) {
            'local' => 1,
            'task' => 2,
            'site' => 3,
            default => 4,
        };
    }
}
