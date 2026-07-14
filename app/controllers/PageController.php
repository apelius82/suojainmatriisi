<?php
declare(strict_types=1);

final class PageController
{
    public function __construct(
        private LibraryRepository $library,
        private RequirementRepository $requirements,
        private AuditRepository $audit,
        private RequirementResolver $resolver,
        private EnvironmentRepository $environments,
        private ZoneRepository $zones
    ) {
    }

    public function dashboard(): array
    {
        return [
            'environments' => $this->environments->all(),
            'sites'        => $this->library->allSites(),
            'zones'        => $this->zones->all(),
            'tasks'        => $this->library->allTasks(),
            'ppeItems'     => $this->library->allPpeItems(),
            'rules'        => $this->requirements->listRules(null),
            'audit'        => $this->audit->latest(20),
        ];
    }

    public function searchData(): array
    {
        return [
            'environments' => $this->environments->all(),
            'sites'        => $this->library->allSites(),
            'tasks'        => $this->library->allTasks(),
        ];
    }

    public function searchResult(?int $envId, ?int $siteId, ?int $zoneId, ?int $taskId): array
    {
        $rules  = $this->requirements->listRules('published');
        $result = $this->resolver->resolve($rules, $envId, $siteId, $zoneId, $taskId);

        $allPpe = $this->library->allPpeItems();
        $ppeMap = [];
        foreach ($allPpe as $item) {
            $ppeMap[(int)$item['id']] = $item;
        }

        $sections  = $this->resolver->groupSections($result['resolved'], $ppeMap);
        $envData   = $envId  ? $this->environments->find($envId)   : null;
        $siteData  = $siteId ? $this->findSite($siteId)             : null;
        $zoneData  = $zoneId ? $this->zones->find($zoneId)          : null;
        $taskData  = $taskId ? $this->findTask($taskId)              : null;

        return [
            'sections'  => $sections,
            'conflicts' => $result['conflicts'],
            'context'   => [
                'env'  => $envData,
                'site' => $siteData,
                'zone' => $zoneData,
                'task' => $taskData,
            ],
        ];
    }

    /** Rakentaa hakusuodattimelle näkyvät tehtäväkortit ja niiden koonnin */
    public function taskCards(?int $envId, ?int $siteId, ?int $zoneId): array
    {
        $tasks = $this->library->allTasks($envId);
        $cards = [];

        foreach ($tasks as $task) {
            $taskId = (int)$task['id'];
            if ($taskId < 1) {
                continue;
            }

            $result = $this->searchResult($envId, $siteId, $zoneId, $taskId);
            $sections = $result['sections'] ?? ['always' => [], 'conditional' => [], 'other_safety' => [], 'information' => []];
            $summary = [
                'always'       => count($sections['always'] ?? []),
                'conditional'  => count($sections['conditional'] ?? []),
                'other_safety' => count($sections['other_safety'] ?? []),
                'information'  => count($sections['information'] ?? []),
            ];
            $summary['total'] = $summary['always'] + $summary['conditional'] + $summary['other_safety'] + $summary['information'];

            // Suodatettu näkymä: näytä vain tehtävät, joilla on oikeasti sisältöä valittuun kontekstiin.
            if (($envId !== null || $siteId !== null || $zoneId !== null) && $summary['total'] === 0) {
                continue;
            }

            $cards[] = [
                'task'    => $task,
                'result'  => $result,
                'summary' => $summary,
            ];
        }

        return $cards;
    }

    public function zonesBySite(int $siteId): array
    {
        return $this->zones->allBySite($siteId);
    }

    private function findSite(int $id): ?array
    {
        $sites = $this->library->allSites();
        foreach ($sites as $s) {
            if ((int)$s['id'] === $id) return $s;
        }
        return null;
    }

    private function findTask(int $id): ?array
    {
        $tasks = $this->library->allTasks();
        foreach ($tasks as $t) {
            if ((int)$t['id'] === $id) return $t;
        }
        return null;
    }
}
