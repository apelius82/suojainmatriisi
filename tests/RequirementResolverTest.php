<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/services/RequirementResolver.php';

/**
 * Apufunktio sääntörivin rakentamiseen testeissä.
 */
function make_rule(
    string $scope,
    string $level,
    int $ppeId,
    ?int $envId = null,
    ?int $siteId = null,
    ?int $zoneId = null,
    ?int $taskId = null,
    string $status = 'published',
    int $id = 0
): array {
    static $counter = 0;
    return [
        'id'                => $id ?: ++$counter,
        'scope_type'        => $scope,
        'requirement_level' => $level,
        'ppe_item_id'       => $ppeId,
        'environment_id'    => $envId,
        'site_id'           => $siteId,
        'zone_id'           => $zoneId,
        'task_id'           => $taskId,
        'status'            => $status,
        'notes'             => null,
        'condition_text'    => null,
    ];
}

// -------------------------------------------------------------------
// TEST 1: Yleinen vaatimus periytyy ilman muuta kontekstia
// -------------------------------------------------------------------
function test_global_requirement_inherited(): void
{
    $svc   = new RequirementResolver();
    $rules = [
        make_rule('global', 'mandatory', 1),
    ];
    $result = $svc->resolve($rules, null, null, null, null);
    assert(count($result['resolved']) === 1, 'T1: Yleinen sääntö pitää periytyä');
    assert($result['resolved'][0]['requirement_level'] === 'mandatory', 'T1: Taso pitää olla mandatory');
}

// -------------------------------------------------------------------
// TEST 2: Työmaan tiukempi vaatimus voittaa yleisen
// -------------------------------------------------------------------
function test_site_overrides_global(): void
{
    $svc   = new RequirementResolver();
    $rules = [
        make_rule('global',      'recommended', 1),
        make_rule('site',        'mandatory',   1, null, 5),
    ];
    $result = $svc->resolve($rules, null, 5, null, null);
    assert(count($result['resolved']) === 1, 'T2: Pitää olla 1 sääntö');
    assert($result['resolved'][0]['requirement_level'] === 'mandatory', 'T2: Mandatory voittaa recommended');
}

// -------------------------------------------------------------------
// TEST 3: Aluekohtainen voittaa työmaan
// -------------------------------------------------------------------
function test_zone_overrides_site(): void
{
    $svc   = new RequirementResolver();
    $rules = [
        make_rule('site',  'recommended', 2, null, 5),
        make_rule('zone',  'mandatory',   2, null, 5, 10),
    ];
    $result = $svc->resolve($rules, null, 5, 10, null);
    assert(count($result['resolved']) === 1, 'T3: Pitää olla 1 sääntö');
    // Zone on spesifisempi (scopeLevel 4) kuin site (3), joten zone voittaa
    $winner = $result['resolved'][0];
    assert($winner['requirement_level'] === 'mandatory', 'T3: Zone mandatory voittaa site recommended');
    assert($winner['_source_scope'] === 'zone', 'T3: Lähde pitää olla zone');
}

// -------------------------------------------------------------------
// TEST 4: Ehdollinen vaatimus aktivoituu oikealla kontekstilla
// -------------------------------------------------------------------
function test_conditional_activates_with_task(): void
{
    $svc   = new RequirementResolver();
    $rules = [
        make_rule('task', 'conditional', 3, null, null, null, 7),
    ];
    // Ei tehtävää — ei sääntöä
    $resultNoTask = $svc->resolve($rules, null, null, null, null);
    assert(count($resultNoTask['resolved']) === 0, 'T4a: Ilman taskia ehdollinen ei näy');

    // Oikealla tehtävällä — sääntö aktivoituu
    $resultWithTask = $svc->resolve($rules, null, null, null, 7);
    assert(count($resultWithTask['resolved']) === 1, 'T4b: Oikealla taskilla ehdollinen aktivoituu');
    assert($resultWithTask['resolved'][0]['requirement_level'] === 'conditional', 'T4b: Taso on conditional');
}

// -------------------------------------------------------------------
// TEST 5: Luonnos ei näy (vain julkaistu näkyy)
// -------------------------------------------------------------------
function test_draft_not_visible(): void
{
    $svc   = new RequirementResolver();
    $rules = [
        make_rule('global', 'mandatory', 1, null, null, null, null, 'draft'),
        make_rule('global', 'mandatory', 2, null, null, null, null, 'published'),
    ];
    $result = $svc->resolve($rules, null, null, null, null);
    assert(count($result['resolved']) === 1, 'T5: Vain julkaistu näkyy');
    assert((int)$result['resolved'][0]['ppe_item_id'] === 2, 'T5: PPE id pitää olla 2 (julkaistu)');
}

// -------------------------------------------------------------------
// TEST 6: Ympäristövaatimus suodattuu oikein
// -------------------------------------------------------------------
function test_environment_scope_filtered(): void
{
    $svc   = new RequirementResolver();
    $rules = [
        make_rule('environment', 'mandatory', 5, 1),  // env 1
        make_rule('environment', 'mandatory', 6, 2),  // env 2
    ];
    // Haetaan env 1:llä
    $result = $svc->resolve($rules, 1, null, null, null);
    assert(count($result['resolved']) === 1, 'T6: Vain env 1:n sääntö');
    assert((int)$result['resolved'][0]['ppe_item_id'] === 5, 'T6: PPE id 5');
}

// -------------------------------------------------------------------
// TEST 7: Avolouhos + Poraus — käytännön integraatiotesti
// -------------------------------------------------------------------
function test_avolouhos_poraus_scenario(): void
{
    $svc   = new RequirementResolver();
    $envId = 1; $taskId = 3; // Poraus

    $rules = [
        // Yleiset ympäristövaatimukset (scope=environment)
        make_rule('environment', 'mandatory', 10, $envId),  // kypärä EN 397
        make_rule('environment', 'mandatory', 11, $envId),  // kuulonsuojaimet
        make_rule('environment', 'mandatory', 12, $envId),  // näkyvä vaatetus
        // Poraus-spesifit (scope=task)
        make_rule('task', 'mandatory', 10, $envId, null, null, $taskId),  // kypärä EN 397
        make_rule('task', 'mandatory', 13, $envId, null, null, $taskId),  // FFP3
        make_rule('task', 'mandatory', 15, $envId, null, null, $taskId),  // tiiviit lasit
    ];

    $result = $svc->resolve($rules, $envId, null, null, $taskId);
    $resolved = $result['resolved'];

    // Pitää löytyä 4 eri PPE-tuotetta: 10, 11, 12, 13, 15
    $ppeIds = array_map(fn($r) => (int)$r['ppe_item_id'], $resolved);
    sort($ppeIds);
    assert(in_array(10, $ppeIds, true), 'T7: Kypärä 10 löytyy');
    assert(in_array(11, $ppeIds, true), 'T7: Kuulonsuojain 11 löytyy');
    assert(in_array(12, $ppeIds, true), 'T7: Näkyvä vaatetus 12 löytyy');
    assert(in_array(13, $ppeIds, true), 'T7: FFP3 13 löytyy');
    assert(in_array(15, $ppeIds, true), 'T7: Tiiviit lasit 15 löytyy');
    assert(count($resolved) === 5, 'T7: Yhteensä 5 eri vaatimusta');
}

// -------------------------------------------------------------------
// TEST 8: Tiukempi vaatimustaso voittaa lievemmän eri tasoilta
// -------------------------------------------------------------------
function test_stricter_level_wins_across_scopes(): void
{
    $svc   = new RequirementResolver();
    $rules = [
        make_rule('environment', 'mandatory',   20, 1),           // ympäristö: pakollinen
        make_rule('task',        'recommended', 20, 1, null, null, 5), // tehtävä: suositeltu
    ];
    $result = $svc->resolve($rules, 1, null, null, 5);
    assert(count($result['resolved']) === 1, 'T8: 1 sääntö');
    // Task on spesifisempi, mutta environment on tiukempi -> mandatory voittaa
    assert($result['resolved'][0]['requirement_level'] === 'mandatory', 'T8: Mandatory voittaa recommended');
}

// -------------------------------------------------------------------
// TEST 9: Scope-tason prioriteetti on oikein (scopeLevel)
// -------------------------------------------------------------------
function test_scope_level_priority_order(): void
{
    $svc = new RequirementResolver();
    // Korkein = spesifisin
    assert($svc->scopeLevel('exception')  === 9, 'T9: exception=9');
    assert($svc->scopeLevel('phase')      === 8, 'T9: phase=8');
    assert($svc->scopeLevel('zone_task')  === 7, 'T9: zone_task=7');
    assert($svc->scopeLevel('site_task')  === 6, 'T9: site_task=6');
    assert($svc->scopeLevel('local')      === 6, 'T9: local=6 (alias)');
    assert($svc->scopeLevel('task')       === 5, 'T9: task=5');
    assert($svc->scopeLevel('zone')       === 4, 'T9: zone=4');
    assert($svc->scopeLevel('site')       === 3, 'T9: site=3');
    assert($svc->scopeLevel('environment')=== 2, 'T9: environment=2');
    assert($svc->scopeLevel('global')     === 1, 'T9: global=1');
}

// -------------------------------------------------------------------
// TEST 10: groupSections jakaa kortit oikein osioihin
// -------------------------------------------------------------------
function test_group_sections(): void
{
    $svc = new RequirementResolver();
    $resolved = [
        make_rule('global', 'mandatory',  1) + ['_source_scope' => 'global'],
        make_rule('global', 'conditional',2) + ['_source_scope' => 'global'],
        make_rule('global', 'information',3) + ['_source_scope' => 'global'],
        make_rule('global', 'mandatory',  4) + ['_source_scope' => 'global'],
    ];
    $ppeMap = [
        1 => ['id'=>1,'name'=>'PPE A','icon'=>'a.svg','item_class'=>'personal_protection','standard_ref'=>null],
        2 => ['id'=>2,'name'=>'PPE B','icon'=>'b.svg','item_class'=>'personal_protection','standard_ref'=>null],
        3 => ['id'=>3,'name'=>'PPE C','icon'=>'c.svg','item_class'=>'personal_protection','standard_ref'=>null],
        4 => ['id'=>4,'name'=>'OSE D','icon'=>'d.svg','item_class'=>'other_safety',       'standard_ref'=>null],
    ];
    $sections = $svc->groupSections($resolved, $ppeMap);
    assert(count($sections['always'])      === 1, 'T10: 1 aina vaadittava (personal + mandatory)');
    assert(count($sections['conditional']) === 1, 'T10: 1 ehdollinen');
    assert(count($sections['information']) === 1, 'T10: 1 ohje');
    assert(count($sections['other_safety'])=== 1, 'T10: 1 muu turvallisuusvaruste');
}
