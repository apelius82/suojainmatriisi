<?php
$activeTab = (string)($_GET['tab'] ?? 'environments');
$validTabs = ['environments','sites','zones','tasks','ppe','rules','audit'];
if (!in_array($activeTab, $validTabs, true)) $activeTab = 'environments';
$lang = $_SESSION['sm_lang'] ?? 'fi';

$tabIcons = [
    'environments' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
    'sites'        => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 3H8l-2 4h12l-2-4z"/></svg>',
    'zones'        => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="4"/></svg>',
    'tasks'        => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>',
    'ppe'          => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
    'rules'        => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>',
    'audit'        => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
];
$tabCounts = [
    'environments' => count($data['environments']),
    'sites'  => count($data['sites']),
    'zones'  => count($data['zones']),
    'tasks'  => count($data['tasks']),
    'ppe'    => count($data['ppeItems']),
    'rules'  => count($data['rules']),
    'audit'  => count($data['audit']),
];
$tabs = [
    'environments' => sm_t('tab_environments', $lang),
    'sites'        => sm_t('tab_sites', $lang),
    'zones'        => sm_t('tab_zones', $lang),
    'tasks'        => sm_t('tab_tasks', $lang),
    'ppe'          => sm_t('tab_ppe', $lang),
    'rules'        => sm_t('tab_rules', $lang),
    'audit'        => sm_t('tab_audit', $lang),
];
?>

<div class="sm-page-header">
  <div class="sm-page-header-left">
    <h1><?= sm_h(sm_t('nav_dashboard', $lang)) ?></h1>
    <p class="sm-page-subtitle">Hallinnoi toimintaympäristöt, työmaat, alueet, työlajit, suojaimet ja vaatimussäännöt</p>
  </div>
</div>

<nav class="sm-admin-tabs" role="tablist" aria-label="Hallintaosiot">
  <?php foreach ($tabs as $key => $label): ?>
    <a href="<?= sm_h(sm_base_url()) ?>/index.php?page=dashboard&tab=<?= sm_h($key) ?>"
       role="tab"
       class="sm-admin-tab <?= $activeTab === $key ? 'active' : '' ?>"
       aria-selected="<?= $activeTab === $key ? 'true' : 'false' ?>">
      <?= $tabIcons[$key] ?? '' ?>
      <?= sm_h($label) ?>
      <span class="sm-tab-badge"><?= (int)$tabCounts[$key] ?></span>
    </a>
  <?php endforeach; ?>
</nav>

<?php if ($activeTab === 'environments'): ?>
<section aria-labelledby="tab-env-heading">
  <div class="sm-section-header">
    <h2 class="sm-section-title" id="tab-env-heading"><?= sm_h(sm_t('tab_environments', $lang)) ?></h2>
  </div>
  <div class="sm-card">
    <?php if (empty($data['environments'])): ?>
      <div class="sm-empty"><p>Ei toimintaympäristöjä. Lisää ensimmäinen alla.</p></div>
    <?php else: ?>
      <div class="sm-table-wrap">
        <table class="sm-table"><thead><tr><th>Nimi</th><th>Koodi</th><th>Kuvaus</th></tr></thead><tbody>
        <?php foreach ($data['environments'] as $env): ?>
          <tr><td><strong><?= sm_h($env['name']) ?></strong></td><td><code><?= sm_h($env['code']) ?></code></td><td class="sm-text-muted"><?= sm_h((string)($env['description'] ?? '')) ?></td></tr>
        <?php endforeach; ?>
        </tbody></table>
      </div>
    <?php endif; ?>
    <div class="sm-add-form-block">
      <h3>+ Lisää uusi toimintaympäristö</h3>
      <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=save_environment" class="sm-form-row">
        <?= sm_csrf_field() ?>
        <label>Nimi <input name="name" required autocomplete="off" placeholder="Esim. Avolouhos"></label>
        <label>Koodi <input name="code" required autocomplete="off" placeholder="Esim. ENV-AVOL"></label>
        <label class="sm-form-full">Kuvaus <input name="description" autocomplete="off"></label>
        <label style="align-self:end"><button class="sm-btn sm-btn-primary" type="submit">Tallenna</button></label>
      </form>
    </div>
  </div>
</section>

<?php elseif ($activeTab === 'sites'): ?>
<section aria-labelledby="tab-sites-heading">
  <div class="sm-section-header">
    <h2 class="sm-section-title" id="tab-sites-heading"><?= sm_h(sm_t('tab_sites', $lang)) ?></h2>
  </div>
  <div class="sm-card">
    <?php if (empty($data['sites'])): ?>
      <div class="sm-empty"><p>Ei työmaarekisteröintejä.</p></div>
    <?php else: ?>
      <div class="sm-table-wrap">
        <table class="sm-table"><thead><tr><th>Nimi</th><th>Koodi</th><th>Toimintaympäristö</th></tr></thead><tbody>
        <?php foreach ($data['sites'] as $site):
          $envName = '';
          foreach ($data['environments'] as $e) {
            if ((int)$e['id'] === (int)($site['environment_id'] ?? 0)) { $envName = $e['name']; break; }
          }
        ?>
          <tr>
            <td><strong><?= sm_h($site['name']) ?></strong></td>
            <td><code><?= sm_h($site['code']) ?></code></td>
            <td class="sm-text-muted"><?= $envName ? sm_h($envName) : '<span>—</span>' ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody></table>
      </div>
    <?php endif; ?>
    <div class="sm-add-form-block">
      <h3>+ Lisää uusi työmaa</h3>
      <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=save_site" class="sm-form-row">
        <?= sm_csrf_field() ?>
        <label>Nimi <input name="name" required autocomplete="off" placeholder="Työmaan nimi"></label>
        <label>Koodi <input name="code" required autocomplete="off" placeholder="Esim. TM-001"></label>
        <label>Toimintaympäristö
          <select name="environment_id">
            <option value="0">— valitse —</option>
            <?php foreach ($data['environments'] as $env): ?>
              <option value="<?= (int)$env['id'] ?>"><?= sm_h($env['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label style="align-self:end"><button class="sm-btn sm-btn-primary" type="submit">Tallenna</button></label>
      </form>
    </div>
  </div>
</section>

<?php elseif ($activeTab === 'zones'): ?>
<section aria-labelledby="tab-zones-heading">
  <div class="sm-section-header">
    <h2 class="sm-section-title" id="tab-zones-heading"><?= sm_h(sm_t('tab_zones', $lang)) ?></h2>
  </div>
  <div class="sm-card">
    <?php if (empty($data['zones'])): ?>
      <div class="sm-empty"><p>Ei alueita.</p></div>
    <?php else: ?>
      <div class="sm-table-wrap">
        <table class="sm-table"><thead><tr><th>Nimi</th><th>Koodi</th><th>Työmaa</th></tr></thead><tbody>
        <?php foreach ($data['zones'] as $zone): ?>
          <tr>
            <td><strong><?= sm_h($zone['name']) ?></strong></td>
            <td><code><?= sm_h($zone['code']) ?></code></td>
            <td class="sm-text-muted"><?= sm_h((string)($zone['site_name'] ?? '')) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody></table>
      </div>
    <?php endif; ?>
    <div class="sm-add-form-block">
      <h3>+ Lisää uusi alue / laitos</h3>
      <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=save_zone" class="sm-form-row">
        <?= sm_csrf_field() ?>
        <label>Työmaa
          <select name="site_id" required>
            <option value="0">— valitse —</option>
            <?php foreach ($data['sites'] as $site): ?>
              <option value="<?= (int)$site['id'] ?>"><?= sm_h($site['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>Nimi <input name="name" required autocomplete="off" placeholder="Esim. Porauskenttä"></label>
        <label>Koodi <input name="code" required autocomplete="off" placeholder="Esim. Z-001"></label>
        <label class="sm-form-full">Kuvaus <input name="description" autocomplete="off"></label>
        <label style="align-self:end"><button class="sm-btn sm-btn-primary" type="submit">Tallenna</button></label>
      </form>
    </div>
  </div>
</section>

<?php elseif ($activeTab === 'tasks'): ?>
<section aria-labelledby="tab-tasks-heading">
  <div class="sm-section-header">
    <h2 class="sm-section-title" id="tab-tasks-heading"><?= sm_h(sm_t('tab_tasks', $lang)) ?></h2>
  </div>
  <div class="sm-card">
    <?php if (empty($data['tasks'])): ?>
      <div class="sm-empty"><p>Ei tehtävärekisteröintejä.</p></div>
    <?php else: ?>
      <div class="sm-table-wrap">
        <table class="sm-table"><thead><tr><th>Nimi</th><th>Tyyppi</th><th>Kategoria</th></tr></thead><tbody>
        <?php foreach ($data['tasks'] as $task): ?>
          <tr>
            <td><strong><?= sm_h($task['name']) ?></strong></td>
            <td><span class="sm-badge sm-badge-global"><?= sm_h((string)($task['work_type'] ?? 'task')) ?></span></td>
            <td class="sm-text-muted"><?= sm_h($task['category']) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody></table>
      </div>
    <?php endif; ?>
    <div class="sm-add-form-block">
      <h3>+ Lisää uusi työlaji / tehtävä</h3>
      <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=save_task" class="sm-form-row">
        <?= sm_csrf_field() ?>
        <label>Nimi <input name="name" required autocomplete="off" placeholder="Tehtävän nimi"></label>
        <label>Tyyppi
          <select name="work_type">
            <option value="task">Tehtävä</option>
            <option value="work_type">Työlaji</option>
            <option value="position">Vakanssi</option>
          </select>
        </label>
        <label>Kategoria <input name="category" required autocomplete="off" placeholder="Esim. Louhintatyöt"></label>
        <label style="align-self:end"><button class="sm-btn sm-btn-primary" type="submit">Tallenna</button></label>
      </form>
    </div>
  </div>
</section>

<?php elseif ($activeTab === 'ppe'): ?>
<section aria-labelledby="tab-ppe-heading">
  <div class="sm-section-header">
    <h2 class="sm-section-title" id="tab-ppe-heading"><?= sm_h(sm_t('tab_ppe', $lang)) ?></h2>
  </div>
  <div class="sm-card">
    <?php if (empty($data['ppeItems'])): ?>
      <div class="sm-empty"><p>Suojainkirjasto on tyhjä.</p></div>
    <?php else: ?>
      <div class="sm-table-wrap">
        <table class="sm-table"><thead><tr><th style="width:44px"></th><th>Nimi</th><th>Luokka</th><th>Standardi</th><th>Koodi</th></tr></thead><tbody>
        <?php foreach ($data['ppeItems'] as $item):
          $cls = (string)($item['item_class'] ?? 'personal_protection');
        ?>
          <tr>
            <td><img src="<?= sm_h(sm_base_url()) ?>/assets/img/ppe/<?= sm_h((string)$item['icon']) ?>" alt="" width="28" height="28" style="display:block;object-fit:contain;"></td>
            <td><strong><?= sm_h($item['name']) ?></strong></td>
            <td><span class="sm-badge <?= $cls === 'other_safety' ? 'sm-badge-global' : 'sm-badge-mandatory' ?>"><?= sm_h(sm_t($cls, $lang)) ?></span></td>
            <td class="sm-text-muted"><?= sm_h((string)($item['standard_ref'] ?? '')) ?></td>
            <td><code><?= sm_h($item['code']) ?></code></td>
          </tr>
        <?php endforeach; ?>
        </tbody></table>
      </div>
    <?php endif; ?>
    <div class="sm-add-form-block">
      <h3>+ Lisää uusi suojain</h3>
      <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=save_ppe" class="sm-rule-form-grid">
        <?= sm_csrf_field() ?>
        <label>Koodi <input name="code" required autocomplete="off" placeholder="PPE-001"></label>
        <label>Nimi <input name="name" required autocomplete="off" placeholder="Kypärä"></label>
        <label>Luokka
          <select name="item_class">
            <option value="personal_protection"><?= sm_h(sm_t('personal_protection', $lang)) ?></option>
            <option value="other_safety"><?= sm_h(sm_t('other_safety', $lang)) ?></option>
          </select>
        </label>
        <label>Kategoria <input name="category" required autocomplete="off" placeholder="Pään suojaus"></label>
        <label>Standardi <input name="standard_ref" autocomplete="off" placeholder="EN 397"></label>
        <label>Ikoni <input name="icon" value="helmet.svg" required autocomplete="off"><span class="sm-label-hint">Tiedostonimi esim. <code>helmet.svg</code></span></label>
        <div class="sm-form-full"><button class="sm-btn sm-btn-primary" type="submit">Tallenna suojain</button></div>
      </form>
    </div>
  </div>
</section>

<?php elseif ($activeTab === 'rules'): ?>
<section aria-labelledby="tab-rules-heading">
  <div class="sm-section-header">
    <h2 class="sm-section-title" id="tab-rules-heading"><?= sm_h(sm_t('tab_rules', $lang)) ?></h2>
  </div>

  <?php if (!empty($data['rules'])): ?>
  <div class="sm-card" style="padding:0;overflow:hidden;">
    <div class="sm-table-wrap">
      <table class="sm-table"><thead><tr>
        <th>Taso</th><th>Kohde</th><th>Suojain</th><th>Vaatimus</th><th>Tila</th><th>Ver.</th><th>Muuta</th>
      </tr></thead><tbody>
      <?php foreach ($data['rules'] as $rule):
        $scopeKey  = (string)$rule['scope_type'];
        $levelKey  = (string)$rule['requirement_level'];
        $statusKey = (string)$rule['status'];
        $scopeMap  = ['global'=>'Yleinen','environment'=>'Ympäristö','site'=>'Työmaa','zone'=>'Alue',
                      'task'=>'Tehtävä','site_task'=>'Työmaa+tehtävä','local'=>'Paikallinen',
                      'zone_task'=>'Alue+tehtävä','phase'=>'Työvaihe','exception'=>'Poikkeus'];
      ?>
        <tr>
          <td><span class="sm-badge sm-badge-<?= sm_h($scopeKey) ?>"><?= sm_h($scopeMap[$scopeKey] ?? $scopeKey) ?></span></td>
          <td>
            <?php if (!empty($rule['env_name'])): ?><small class="sm-text-muted"><?= sm_h((string)$rule['env_name']) ?></small><br><?php endif; ?>
            <?php if (!empty($rule['site_name'])): ?><small><?= sm_h((string)$rule['site_name']) ?></small><?php endif; ?>
            <?php if (!empty($rule['task_name'])): ?><br><small><?= sm_h((string)$rule['task_name']) ?></small><?php endif; ?>
          </td>
          <td><strong><?= sm_h((string)$rule['ppe_name']) ?></strong></td>
          <td>
            <span class="sm-badge sm-badge-<?= sm_h($levelKey) ?>"><?= sm_h(sm_t($levelKey, $lang)) ?></span>
            <?php if (!empty($rule['condition_text'])): ?>
              <span class="sm-label-hint" style="display:block;font-size:.75rem"><?= sm_h(mb_strimwidth((string)$rule['condition_text'], 0, 55, '…')) ?></span>
            <?php endif; ?>
          </td>
          <td><span class="sm-badge sm-badge-<?= sm_h($statusKey) ?>"><?= sm_h(sm_t($statusKey, $lang)) ?></span></td>
          <td style="color:var(--sm-muted);font-size:.85rem"><?= (int)$rule['version_no'] ?></td>
          <td>
            <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=change_rule_status" class="sm-rule-status-form">
              <?= sm_csrf_field() ?>
              <input type="hidden" name="rule_id" value="<?= (int)$rule['id'] ?>">
              <select name="status">
                <?php foreach (['draft'=>'Luonnos','review'=>'Tarkastettavana','approved'=>'Hyväksytty','published'=>'Julkaistu','archived'=>'Arkistoitu'] as $v=>$l): ?>
                  <option value="<?= sm_h($v) ?>" <?= $statusKey === $v ? 'selected' : '' ?>><?= sm_h($l) ?></option>
                <?php endforeach; ?>
              </select>
              <button class="sm-btn sm-btn-secondary sm-btn-sm" type="submit">Siirrä</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody></table>
    </div>
  </div>
  <?php else: ?>
    <div class="sm-empty"><p>Vaatimussääntöjä ei ole vielä lisätty.</p></div>
  <?php endif; ?>

  <div class="sm-card sm-add-form-block" style="border-style:dashed;background:var(--sm-surface-2)">
    <h3>+ Lisää uusi vaatimussääntö</h3>
    <div class="sm-status-strip">
      <?php foreach (['draft'=>'Luonnos','review'=>'Tarkastettavana','approved'=>'Hyväksytty','published'=>'Julkaistu','archived'=>'Arkistoitu'] as $v=>$l): ?>
        <span class="sm-status-step sm-badge sm-badge-<?= sm_h($v) ?>"><?= sm_h($l) ?></span>
      <?php endforeach; ?>
    </div>
    <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=save_rule" class="sm-rule-form-grid">
      <?= sm_csrf_field() ?>
      <label>Scope-taso
        <select name="scope_type">
          <option value="global">Yleinen (org)</option>
          <option value="environment">Toimintaympäristö</option>
          <option value="site">Työmaa</option>
          <option value="zone">Alue</option>
          <option value="task">Työlaji (yleinen)</option>
          <option value="site_task">Työmaa + työlaji</option>
          <option value="zone_task">Alue + työlaji</option>
          <option value="phase">Työvaihe</option>
          <option value="exception">Poikkeus</option>
        </select>
      </label>
      <label>Toimintaympäristö
        <select name="environment_id">
          <option value="0">— ei rajausta —</option>
          <?php foreach ($data['environments'] as $env): ?>
            <option value="<?= (int)$env['id'] ?>"><?= sm_h($env['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Työmaa
        <select name="site_id">
          <option value="0">— ei rajausta —</option>
          <?php foreach ($data['sites'] as $site): ?>
            <option value="<?= (int)$site['id'] ?>"><?= sm_h($site['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Alue
        <select name="zone_id">
          <option value="0">— ei rajausta —</option>
          <?php foreach ($data['zones'] as $zone): ?>
            <option value="<?= (int)$zone['id'] ?>"><?= sm_h($zone['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Työlaji
        <select name="task_id">
          <option value="0">— ei rajausta —</option>
          <?php foreach ($data['tasks'] as $task): ?>
            <option value="<?= (int)$task['id'] ?>"><?= sm_h($task['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Suojain
        <select name="ppe_item_id">
          <?php foreach ($data['ppeItems'] as $item): ?>
            <option value="<?= (int)$item['id'] ?>"><?= sm_h($item['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Vaatimustaso
        <select name="requirement_level">
          <option value="mandatory"><?= sm_h(sm_t('mandatory', $lang)) ?></option>
          <option value="conditional"><?= sm_h(sm_t('conditional', $lang)) ?></option>
          <option value="recommended"><?= sm_h(sm_t('recommended', $lang)) ?></option>
          <option value="information"><?= sm_h(sm_t('information', $lang)) ?></option>
          <option value="not_applicable"><?= sm_h(sm_t('not_applicable', $lang)) ?></option>
          <option value="prohibited"><?= sm_h(sm_t('prohibited', $lang)) ?></option>
        </select>
      </label>
      <label>Tila
        <select name="status">
          <option value="draft">Luonnos</option>
          <option value="review">Tarkastettavana</option>
          <option value="approved">Hyväksytty</option>
          <option value="published">Julkaistu</option>
        </select>
      </label>
      <label class="sm-form-full">Ehto
        <input name="condition_text" autocomplete="off" placeholder="Milloin aktivoituu">
      </label>
      <label class="sm-form-full">Huomio
        <input name="notes" autocomplete="off" placeholder="Lisätiedot tai perustelu">
      </label>
      <label class="sm-form-full">Muutoskuvaus
        <input name="change_description" autocomplete="off" placeholder="Muutos versiointia varten">
      </label>
      <div class="sm-form-full"><button class="sm-btn sm-btn-primary" type="submit">Tallenna sääntö</button></div>
    </form>
  </div>
</section>

<?php elseif ($activeTab === 'audit'): ?>
<section aria-labelledby="tab-audit-heading">
  <div class="sm-section-header">
    <h2 class="sm-section-title" id="tab-audit-heading"><?= sm_h(sm_t('tab_audit', $lang)) ?></h2>
  </div>
  <div class="sm-card">
    <?php if (empty($data['audit'])): ?>
      <div class="sm-empty"><p>Ei kirjauksia vielä.</p></div>
    <?php else: ?>
      <ul class="sm-audit-list">
        <?php foreach ($data['audit'] as $entry): ?>
          <li>
            <span class="sm-audit-event"><?= sm_h((string)$entry['event_type']) ?></span>
            <span class="sm-audit-user"><?= sm_h((string)$entry['display_name']) ?></span>
            <span class="sm-audit-time"><?= sm_h((string)$entry['created_at']) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>
