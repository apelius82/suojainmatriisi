<?php
declare(strict_types=1);

/**
 * RequirementResolver
 *
 * Yhdistää suojainvaatimukset kaikista tasoista seuraavassa prioriteettijärjestyksessä
 * (korkein prioriteetti = ylikirjoittaa muut):
 *
 * 9. nimenomainen hyväksytty poikkeus        (exception)
 * 8. työvaihe- tai tilannekohtainen          (phase)
 * 7. alue + työn nimike -yhdistelmä          (zone_task)
 * 6. työmaa + työn nimike -yhdistelmä        (site_task / local)
 * 5. työn nimikkeen yleinen vaatimus         (task)
 * 4. alueen vaatimus                         (zone)
 * 3. työmaan vaatimus                        (site)
 * 2. toimintaympäristön vaatimus             (environment)
 * 1. organisaation yleinen vaatimus          (global)
 *
 * Lisäksi: tiukempi vaatimustaso voittaa lievemmän saman PPE-tuotteen eri tasoilla.
 *
 * @param array<int,array<string,mixed>> $rules     Kaikki julkaistut säännöt
 * @param int|null $envId    Toimintaympäristö
 * @param int|null $siteId   Työmaa / toimipaikka
 * @param int|null $zoneId   Alue / laitos / osasto
 * @param int|null $taskId   Työlaji / tehtävä / vakanssi
 */
final class RequirementResolver
{
    /**
     * @return array{
     *   resolved: array<int,array<string,mixed>>,
     *   conflicts: array<int,array<string,mixed>>
     * }
     */
    public function resolve(
        array $rules,
        ?int $envId,
        ?int $siteId,
        ?int $zoneId,
        ?int $taskId
    ): array {
        // 1. Suodata soveltuvat säännöt kontekstin mukaan
        $eligible = array_values(array_filter($rules, function (array $rule) use ($envId, $siteId, $zoneId, $taskId): bool {
            if (($rule['status'] ?? '') !== 'published') {
                return false;
            }
            return match ($rule['scope_type']) {
                'global'    => true,
                'environment' => $envId !== null && (int)$rule['environment_id'] === $envId,
                'site'      => $siteId !== null && (int)$rule['site_id'] === $siteId,
                'zone'      => $zoneId !== null && (int)$rule['zone_id'] === $zoneId,
                'task'      => $taskId !== null && (int)$rule['task_id'] === $taskId,
                'site_task', 'local' => $siteId !== null && $taskId !== null
                    && (int)$rule['site_id'] === $siteId
                    && (int)$rule['task_id'] === $taskId,
                'zone_task' => $zoneId !== null && $taskId !== null
                    && (int)$rule['zone_id'] === $zoneId
                    && (int)$rule['task_id'] === $taskId,
                'phase'     => $taskId !== null && (int)$rule['task_id'] === $taskId,
                'exception' => $siteId !== null && $taskId !== null
                    && (int)$rule['site_id'] === $siteId
                    && (int)$rule['task_id'] === $taskId,
                default => false,
            };
        }));

        // 2. Ryhmittele PPE-tuotteen mukaan
        $grouped = [];
        foreach ($eligible as $rule) {
            $grouped[(int)$rule['ppe_item_id']][] = $rule;
        }

        $resolved = [];
        $conflicts = [];

        foreach ($grouped as $ppeId => $applicableRules) {
            // 3. Järjestä spesifisyyden mukaan (korkein ensin)
            usort($applicableRules, fn(array $a, array $b): int =>
                $this->scopeLevel($b['scope_type']) <=> $this->scopeLevel($a['scope_type'])
            );

            // 4. Valitse tiukin vaatimustaso kaikista soveltuvista säännöistä
            $mostSpecificRule = $applicableRules[0];  // korkein scope-taso
            $strictestRule    = $mostSpecificRule;

            foreach ($applicableRules as $rule) {
                if ($this->strictness($rule['requirement_level']) > $this->strictness($strictestRule['requirement_level'])) {
                    $strictestRule = $rule;
                }
            }

            // 5. Jos tiukin vaatimus tulee eri tasolta kuin spesifisin, merkitse se lähteeksi
            $winner = $strictestRule;
            $winner['_scope_level']       = $this->scopeLevel($winner['scope_type']);
            $winner['_source_scope']      = $winner['scope_type'];
            $winner['_specific_scope']    = $mostSpecificRule['scope_type'];

            // 6. Konflikti: spesifisin taso on eri kuin tiukin
            if ($mostSpecificRule['id'] !== $strictestRule['id']
                && $mostSpecificRule['requirement_level'] !== $strictestRule['requirement_level']
            ) {
                $conflicts[] = [
                    'ppe_item_id' => $ppeId,
                    'kept'        => $winner,
                    'discarded'   => $mostSpecificRule,
                    'reason'      => 'stricter_level_wins',
                ];
            }

            $resolved[$ppeId] = $winner;
        }

        return [
            'resolved'  => array_values($resolved),
            'conflicts' => $conflicts,
        ];
    }

    /**
     * Muodosta tuloskorteista sektioittain jaoteltu näkymä.
     *
     * @param array<int,array<string,mixed>> $resolved
     * @param array<int,array<string,mixed>> $ppeMap  [id => ppe_item row]
     * @return array{
     *   always: array<int,array<string,mixed>>,
     *   conditional: array<int,array<string,mixed>>,
     *   other_safety: array<int,array<string,mixed>>,
     *   information: array<int,array<string,mixed>>
     * }
     */
    public function groupSections(array $resolved, array $ppeMap): array
    {
        $sections = [
            'always'      => [],   // A) aina vaadittavat
            'conditional' => [],   // B) tilanteen mukaan
            'other_safety'=> [],   // C) muut turvallisuusvarusteet
            'information' => [],   // D) kriittiset huomiot
        ];

        foreach ($resolved as $rule) {
            $ppeId   = (int)$rule['ppe_item_id'];
            $ppe     = $ppeMap[$ppeId] ?? null;
            if ($ppe === null) {
                continue;
            }

            $card  = ['rule' => $rule, 'ppe' => $ppe];
            $level = $rule['requirement_level'];
            $class = $ppe['item_class'] ?? 'personal_protection';

            if ($level === 'information') {
                $sections['information'][] = $card;
            } elseif ($level === 'conditional') {
                $sections['conditional'][] = $card;
            } elseif ($class === 'other_safety' && in_array($level, ['mandatory','required','recommended'], true)) {
                $sections['other_safety'][] = $card;
            } elseif (in_array($level, ['mandatory','required'], true) && $class === 'personal_protection') {
                $sections['always'][] = $card;
            } elseif (in_array($level, ['recommended'], true)) {
                $sections['conditional'][] = $card;
            }
        }

        return $sections;
    }

    /**
     * Scope-tason numeerinen arvo: suurempi = spesifisempi = ylikirjoittaa.
     */
    public function scopeLevel(string $scope): int
    {
        return match ($scope) {
            'global'               => 1,
            'environment'          => 2,
            'site'                 => 3,
            'zone'                 => 4,
            'task'                 => 5,
            'site_task', 'local'   => 6,
            'zone_task'            => 7,
            'phase'                => 8,
            'exception'            => 9,
            default                => 0,
        };
    }

    /**
     * Vaatimustason tiukkuus: suurempi = tiukempi.
     */
    public function strictness(string $level): int
    {
        return match ($level) {
            'mandatory', 'required'      => 6,
            'conditional'               => 5,
            'recommended'               => 4,
            'information'               => 3,
            'not_applicable'            => 2,
            'prohibited', 'forbidden'   => 1,
            default                     => 0,
        };
    }

    /**
     * Scope-tason ihmisluettava kuvaus.
     */
    public function scopeLabel(string $scope): string
    {
        return match ($scope) {
            'global'               => 'Yleinen vaatimus',
            'environment'          => 'Toimintaympäristövaatimus',
            'site'                 => 'Työmaakohtainen',
            'zone'                 => 'Aluekohtainen',
            'task'                 => 'Tehtäväkohtainen',
            'site_task', 'local'   => 'Työmaa + tehtävä',
            'zone_task'            => 'Alue + tehtävä',
            'phase'                => 'Työvaihe',
            'exception'            => 'Hyväksytty poikkeus',
            default                => $scope,
        };
    }
}
