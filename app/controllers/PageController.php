<?php
declare(strict_types=1);

final class PageController
{
    public function __construct(
        private LibraryRepository $library,
        private RequirementRepository $requirements,
        private AuditRepository $audit,
        private RequirementInheritanceService $resolver
    ) {
    }

    public function dashboard(): array
    {
        return [
            'sites' => $this->library->allSites(),
            'tasks' => $this->library->allTasks(),
            'ppeItems' => $this->library->allPpeItems(),
            'rules' => $this->requirements->listRules(null),
            'audit' => $this->audit->latest(20),
        ];
    }

    public function searchResult(?int $siteId, ?int $taskId): array
    {
        $rules = $this->requirements->listRules('published');
        $resolved = $this->resolver->resolve($rules, $siteId, $taskId);
        $allPpe = $this->library->allPpeItems();
        $map = [];
        foreach ($allPpe as $item) {
            $map[(int)$item['id']] = $item;
        }

        $cards = [];
        foreach ($resolved['resolved'] as $rule) {
            $ppeId = (int)$rule['ppe_item_id'];
            if (isset($map[$ppeId])) {
                $cards[] = ['rule' => $rule, 'ppe' => $map[$ppeId]];
            }
        }

        return ['cards' => $cards, 'conflicts' => $resolved['conflicts']];
    }
}
