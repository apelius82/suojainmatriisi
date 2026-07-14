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
        $publishedRules = $this->requirements->listRules('published');
        $taskMeta = $this->buildTaskMeta($publishedRules);
        $ppeTaskLinks = $this->buildPpeTaskLinks($publishedRules);

        return [
            'environments' => $this->environments->all(),
            'sites'        => $this->library->allSites(),
            'zones'        => $this->zones->all(),
            'tasks'        => $this->library->allTasks(),
            'ppeItems'     => $this->library->allPpeItems(),
            'rules'        => $this->requirements->listRules(null),
            'audit'        => $this->audit->latest(20),
            'taskMeta'     => $taskMeta,
            'ppeTaskLinks' => $ppeTaskLinks,
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

    public function searchTaskCards(?int $envId, ?int $siteId, ?int $zoneId): array
    {
        $rules = $this->requirements->listRules('published', $envId);
        $tasks = $this->library->allTasks($envId);
        $ctxEnv = $envId ? $this->environments->find($envId) : null;
        $ctxSite = $siteId ? $this->findSite($siteId) : null;
        $ctxZone = $zoneId ? $this->zones->find($zoneId) : null;
        $allPpe = $this->library->allPpeItems();
        $ppeMap = [];
        foreach ($allPpe as $item) {
            $ppeMap[(int)$item['id']] = $item;
        }

        $cards = [];
        foreach ($tasks as $task) {
            $taskId = (int)($task['id'] ?? 0);
            if ($taskId < 1) {
                continue;
            }

            $resolved = $this->resolver->resolve($rules, $envId, $siteId, $zoneId, $taskId);
            $sections = $this->resolver->groupSections($resolved['resolved'], $ppeMap);
            $totalPpe = count($sections['always'] ?? [])
                + count($sections['conditional'] ?? [])
                + count($sections['other_safety'] ?? []);

            if ($totalPpe === 0 && $siteId !== null) {
                continue;
            }

            $notes = [];
            $risks = [];
            foreach (($sections['information'] ?? []) as $info) {
                $text = trim((string)($info['rule']['notes'] ?? ''));
                if ($text === '') {
                    continue;
                }
                if (str_contains(mb_strtolower($text), 'riski')) {
                    $risks[] = $text;
                } else {
                    $notes[] = $text;
                }
            }

            $cards[] = [
                'task' => $task,
                'sections' => $sections,
                'summary' => [
                    'total' => $totalPpe,
                    'always' => count($sections['always'] ?? []),
                    'conditional' => count($sections['conditional'] ?? []),
                    'other' => count($sections['other_safety'] ?? []),
                ],
                'notes' => array_values(array_unique($notes)),
                'risks' => array_values(array_unique($risks)),
                'context' => [
                    'env' => $ctxEnv,
                    'site' => $ctxSite,
                    'zone' => $ctxZone,
                ],
            ];
        }

        usort($cards, static fn(array $a, array $b): int => strcmp((string)$a['task']['name'], (string)$b['task']['name']));
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

    /** @param array<int, array<string, mixed>> $rules */
    private function buildTaskMeta(array $rules): array
    {
        $meta = [];
        foreach ($rules as $rule) {
            $taskId = (int)($rule['task_id'] ?? 0);
            if ($taskId < 1) {
                continue;
            }
            if (!isset($meta[$taskId])) {
                $meta[$taskId] = [
                    'ppeIds' => [],
                    'sites' => [],
                    'zones' => [],
                ];
            }
            $ppeId = (int)($rule['ppe_item_id'] ?? 0);
            if ($ppeId > 0) {
                $meta[$taskId]['ppeIds'][$ppeId] = true;
            }
            $siteName = trim((string)($rule['site_name'] ?? ''));
            if ($siteName !== '') {
                $meta[$taskId]['sites'][$siteName] = true;
            }
            $zoneName = trim((string)($rule['zone_name'] ?? ''));
            if ($zoneName !== '') {
                $meta[$taskId]['zones'][$zoneName] = true;
            }
        }

        foreach ($meta as $taskId => $item) {
            $meta[$taskId] = [
                'ppe_count' => count($item['ppeIds']),
                'sites' => array_keys($item['sites']),
                'zones' => array_keys($item['zones']),
            ];
        }
        return $meta;
    }

    /** @param array<int, array<string, mixed>> $rules */
    private function buildPpeTaskLinks(array $rules): array
    {
        $links = [];
        foreach ($rules as $rule) {
            $ppeId = (int)($rule['ppe_item_id'] ?? 0);
            $taskId = (int)($rule['task_id'] ?? 0);
            $taskName = trim((string)($rule['task_name'] ?? ''));
            if ($ppeId < 1 || $taskId < 1 || $taskName === '') {
                continue;
            }
            if (!isset($links[$ppeId])) {
                $links[$ppeId] = [];
            }
            $links[$ppeId][$taskId] = $taskName;
        }
        foreach ($links as $ppeId => $tasks) {
            asort($tasks);
            $links[$ppeId] = array_values($tasks);
        }
        return $links;
    }
}
