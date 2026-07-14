<?php
$activeTab = (string)($_GET['tab'] ?? 'environments');
$validTabs = ['environments','sites','zones','tasks','ppe','rules','audit'];
if (!in_array($activeTab, $validTabs, true)) $activeTab = 'environments';
$lang = $_SESSION['sm_lang'] ?? 'fi';
$editId = (int)($_GET['edit_id'] ?? 0);

// Notifikaatiot
$bulkAdded = (int)($_GET['bulk_added'] ?? 0);
$errMsg = match ($_GET['error'] ?? '') {
    'invalid_ppe'  => sm_t('err_invalid_ppe', $lang),
    'no_tasks'     => sm_t('err_no_tasks', $lang),
    'upload_failed'=> sm_t('err_upload_failed', $lang),
    'too_large'    => sm_t('err_too_large', $lang),
    'invalid_type' => sm_t('err_invalid_type', $lang),
    'move_failed'  => sm_t('err_move_failed', $lang),
    default        => '',
};

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

// Svgs for action buttons (reusable)
$iconEdit    = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>';
$iconArchive = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>';
?>

<div class="sm-page-header">
  <div class="sm-page-header-left">
    <h1><?= sm_h(sm_t('nav_dashboard', $lang)) ?></h1>
    <p class="sm-page-subtitle">Hallinnoi toimintaympäristöt, työmaat, alueet, työlajit, suojaimet ja vaatimussäännöt</p>
  </div>
</div>

<?php if ($errMsg !== ''): ?>
  <div class="sm-alert sm-alert-warn" role="alert"><?= sm_h($errMsg) ?></div>
<?php endif; ?>
<?php if ($bulkAdded > 0): ?>
  <div class="sm-alert sm-alert-info" role="alert"><?= sm_h(str_replace('{count}', (string)$bulkAdded, sm_t('bulk_add_success', $lang))) ?></div>
<?php endif; ?>

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

<?php /* ====== ENVIRONMENTS ====== */ ?>
<?php if ($activeTab === 'environments'): ?>
<section aria-labelledby="tab-env-heading">
  <div class="sm-section-header">
    <h2 class="sm-section-title" id="tab-env-heading"><?= sm_h(sm_t('tab_environments', $lang)) ?></h2>
  </div>
  <div class="sm-card" style="padding:0">
    <?php if (empty($data['environments'])): ?>
      <div class="sm-empty" style="margin:1.5rem"><p>Ei toimintaympäristöjä. Lisää ensimmäinen alla.</p></div>
    <?php else: ?>
      <div class="sm-list-block" style="border:0;border-radius:0">
        <?php foreach ($data['environments'] as $env):
          $isEditing = ($editId === (int)$env['id'] && $activeTab === 'environments');
        ?>
          <div class="sm-list-row">
            <div class="sm-list-row-main">
              <div class="sm-list-row-name"><?= sm_h($env['name']) ?> <code class="sm-code-pill"><?= sm_h($env['code']) ?></code></div>
              <?php if (!empty($env['description'])): ?>
                <div class="sm-list-row-meta"><?= sm_h((string)$env['description']) ?></div>
              <?php endif; ?>
            </div>
            <div class="sm-list-row-actions">
              <a href="<?= sm_h(sm_base_url()) ?>/index.php?page=dashboard&tab=environments&edit_id=<?= (int)$env['id'] ?>"
                 class="sm-btn sm-btn-ghost sm-btn-sm" title="Muokkaa"><?= $iconEdit ?> Muokkaa</a>
              <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=archive_entity"
                    
 data-confirm="<?= sm_h('Arkistoi toimintaympäristö ' . $env['name'] . '?') ?>" onsubmit="return confirm(this.dataset.confirm)">
                <?= sm_csrf_field() ?>
                <input type="hidden" name="entity_type" value="environment">
                <input type="hidden" name="entity_id" value="<?= (int)$env['id'] ?>">
                <button class="sm-btn sm-btn-ghost sm-btn-sm sm-btn-danger-ghost" title="Arkistoi"><?= $iconArchive ?></button>
              </form>
            </div>
          </div>
          <?php if ($isEditing): ?>
          <div class="sm-inline-edit-form">
            <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=edit_entity" class="sm-form-row">
              <?= sm_csrf_field() ?>
              <input type="hidden" name="entity_type" value="environment">
              <input type="hidden" name="entity_id" value="<?= (int)$env['id'] ?>">
              <label>Nimi <input name="name" required value="<?= sm_h($env['name']) ?>"></label>
              <label>Koodi <input name="code" required value="<?= sm_h($env['code']) ?>"></label>
              <label class="sm-form-full">Kuvaus <input name="description" value="<?= sm_h((string)($env['description'] ?? '')) ?>"></label>
              <div style="display:flex;gap:.5rem;align-items:center">
                <button class="sm-btn sm-btn-primary sm-btn-sm" type="submit">Tallenna</button>
                <a href="<?= sm_h(sm_base_url()) ?>/index.php?page=dashboard&tab=environments" class="sm-btn sm-btn-ghost sm-btn-sm">Peruuta</a>
              </div>
            </form>
          </div>
          <?php endif; ?>
        <?php endforeach; ?>
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

<?php /* ====== SITES ====== */ ?>
<?php elseif ($activeTab === 'sites'): ?>
<section aria-labelledby="tab-sites-heading">
  <div class="sm-section-header">
    <h2 class="sm-section-title" id="tab-sites-heading"><?= sm_h(sm_t('tab_sites', $lang)) ?></h2>
  </div>
  <div class="sm-card" style="padding:0">
    <?php if (empty($data['sites'])): ?>
      <div class="sm-empty" style="margin:1.5rem"><p>Ei työmaarekisteröintejä.</p></div>
    <?php else: ?>
      <div class="sm-list-block" style="border:0;border-radius:0">
        <?php foreach ($data['sites'] as $site):
          $envName = '';
          foreach ($data['environments'] as $e) {
            if ((int)$e['id'] === (int)($site['environment_id'] ?? 0)) { $envName = $e['name']; break; }
          }
          $isEditing = ($editId === (int)$site['id'] && $activeTab === 'sites');
        ?>
          <div class="sm-list-row">
            <div class="sm-list-row-main">
              <div class="sm-list-row-name"><?= sm_h($site['name']) ?> <code class="sm-code-pill"><?= sm_h($site['code']) ?></code></div>
              <?php if ($envName): ?><div class="sm-list-row-meta"><?= sm_h($envName) ?></div><?php endif; ?>
            </div>
            <div class="sm-list-row-actions">
              <a href="<?= sm_h(sm_base_url()) ?>/index.php?page=dashboard&tab=sites&edit_id=<?= (int)$site['id'] ?>"
                 class="sm-btn sm-btn-ghost sm-btn-sm"><?= $iconEdit ?> Muokkaa</a>
              <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=archive_entity"
                    data-confirm="<?= sm_h('Arkistoi työmaa ' . $site['name'] . '?') ?>" onsubmit="return confirm(this.dataset.confirm)">
                <?= sm_csrf_field() ?>
                <input type="hidden" name="entity_type" value="site">
                <input type="hidden" name="entity_id" value="<?= (int)$site['id'] ?>">
                <button class="sm-btn sm-btn-ghost sm-btn-sm sm-btn-danger-ghost" title="Arkistoi"><?= $iconArchive ?></button>
              </form>
            </div>
          </div>
          <?php if ($isEditing): ?>
          <div class="sm-inline-edit-form">
            <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=edit_entity" class="sm-form-row">
              <?= sm_csrf_field() ?>
              <input type="hidden" name="entity_type" value="site">
              <input type="hidden" name="entity_id" value="<?= (int)$site['id'] ?>">
              <label>Nimi <input name="name" required value="<?= sm_h($site['name']) ?>"></label>
              <label>Koodi <input name="code" required value="<?= sm_h($site['code']) ?>"></label>
              <label>Toimintaympäristö
                <select name="environment_id">
                  <option value="0">— valitse —</option>
                  <?php foreach ($data['environments'] as $env): ?>
                    <option value="<?= (int)$env['id'] ?>" <?= (int)($site['environment_id'] ?? 0) === (int)$env['id'] ? 'selected' : '' ?>><?= sm_h($env['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </label>
              <div style="display:flex;gap:.5rem;align-items:center">
                <button class="sm-btn sm-btn-primary sm-btn-sm" type="submit">Tallenna</button>
                <a href="<?= sm_h(sm_base_url()) ?>/index.php?page=dashboard&tab=sites" class="sm-btn sm-btn-ghost sm-btn-sm">Peruuta</a>
              </div>
            </form>
          </div>
          <?php endif; ?>
        <?php endforeach; ?>
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

<?php /* ====== ZONES ====== */ ?>
<?php elseif ($activeTab === 'zones'): ?>
<section aria-labelledby="tab-zones-heading">
  <div class="sm-section-header">
    <h2 class="sm-section-title" id="tab-zones-heading"><?= sm_h(sm_t('tab_zones', $lang)) ?></h2>
  </div>
  <div class="sm-card" style="padding:0">
    <?php if (empty($data['zones'])): ?>
      <div class="sm-empty" style="margin:1.5rem"><p>Ei alueita.</p></div>
    <?php else: ?>
      <div class="sm-list-block" style="border:0;border-radius:0">
        <?php foreach ($data['zones'] as $zone):
          $isEditing = ($editId === (int)$zone['id'] && $activeTab === 'zones');
        ?>
          <div class="sm-list-row">
            <div class="sm-list-row-main">
              <div class="sm-list-row-name"><?= sm_h($zone['name']) ?> <code class="sm-code-pill"><?= sm_h($zone['code']) ?></code></div>
              <div class="sm-list-row-meta"><?= sm_h((string)($zone['site_name'] ?? '')) ?></div>
            </div>
            <div class="sm-list-row-actions">
              <a href="<?= sm_h(sm_base_url()) ?>/index.php?page=dashboard&tab=zones&edit_id=<?= (int)$zone['id'] ?>"
                 class="sm-btn sm-btn-ghost sm-btn-sm"><?= $iconEdit ?> Muokkaa</a>
              <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=archive_entity"
                    data-confirm="<?= sm_h('Arkistoi alue ' . $zone['name'] . '?') ?>" onsubmit="return confirm(this.dataset.confirm)">
                <?= sm_csrf_field() ?>
                <input type="hidden" name="entity_type" value="zone">
                <input type="hidden" name="entity_id" value="<?= (int)$zone['id'] ?>">
                <button class="sm-btn sm-btn-ghost sm-btn-sm sm-btn-danger-ghost" title="Arkistoi"><?= $iconArchive ?></button>
              </form>
            </div>
          </div>
          <?php if ($isEditing): ?>
          <div class="sm-inline-edit-form">
            <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=edit_entity" class="sm-form-row">
              <?= sm_csrf_field() ?>
              <input type="hidden" name="entity_type" value="zone">
              <input type="hidden" name="entity_id" value="<?= (int)$zone['id'] ?>">
              <label>Työmaa
                <select name="site_id" required>
                  <option value="0">— valitse —</option>
                  <?php foreach ($data['sites'] as $site): ?>
                    <option value="<?= (int)$site['id'] ?>" <?= (int)($zone['site_id'] ?? 0) === (int)$site['id'] ? 'selected' : '' ?>><?= sm_h($site['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </label>
              <label>Nimi <input name="name" required value="<?= sm_h($zone['name']) ?>"></label>
              <label>Koodi <input name="code" required value="<?= sm_h($zone['code']) ?>"></label>
              <label class="sm-form-full">Kuvaus <input name="description" value="<?= sm_h((string)($zone['description'] ?? '')) ?>"></label>
              <div style="display:flex;gap:.5rem;align-items:center">
                <button class="sm-btn sm-btn-primary sm-btn-sm" type="submit">Tallenna</button>
                <a href="<?= sm_h(sm_base_url()) ?>/index.php?page=dashboard&tab=zones" class="sm-btn sm-btn-ghost sm-btn-sm">Peruuta</a>
              </div>
            </form>
          </div>
          <?php endif; ?>
        <?php endforeach; ?>
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

<?php /* ====== TASKS ====== */ ?>
<?php elseif ($activeTab === 'tasks'): ?>
<section aria-labelledby="tab-tasks-heading">
  <div class="sm-section-header">
    <h2 class="sm-section-title" id="tab-tasks-heading"><?= sm_h(sm_t('tab_tasks', $lang)) ?></h2>
  </div>
  <div class="sm-card" style="padding:0">
    <?php if (empty($data['tasks'])): ?>
      <div class="sm-empty" style="margin:1.5rem"><p>Ei tehtävärekisteröintejä.</p></div>
    <?php else: ?>
      <div class="sm-list-block" style="border:0;border-radius:0">
        <?php foreach ($data['tasks'] as $task):
          $isEditing = ($editId === (int)$task['id'] && $activeTab === 'tasks');
          $wt = (string)($task['work_type'] ?? 'task');
        ?>
          <div class="sm-list-row">
            <div class="sm-list-row-main">
              <div class="sm-list-row-name"><?= sm_h($task['name']) ?></div>
              <div class="sm-list-row-meta">
                <span class="sm-badge sm-badge-global" style="font-size:.72rem"><?= sm_h($wt) ?></span>
                <span style="color:var(--sm-muted);font-size:.8rem"> <?= sm_h($task['category']) ?></span>
              </div>
            </div>
            <div class="sm-list-row-actions">
              <a href="<?= sm_h(sm_base_url()) ?>/index.php?page=dashboard&tab=tasks&edit_id=<?= (int)$task['id'] ?>"
                 class="sm-btn sm-btn-ghost sm-btn-sm"><?= $iconEdit ?> Muokkaa</a>
              <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=archive_entity"
                    data-confirm="<?= sm_h('Arkistoi tehtävä ' . $task['name'] . '?') ?>" onsubmit="return confirm(this.dataset.confirm)">
                <?= sm_csrf_field() ?>
                <input type="hidden" name="entity_type" value="task">
                <input type="hidden" name="entity_id" value="<?= (int)$task['id'] ?>">
                <button class="sm-btn sm-btn-ghost sm-btn-sm sm-btn-danger-ghost" title="Arkistoi"><?= $iconArchive ?></button>
              </form>
            </div>
          </div>
          <?php if ($isEditing): ?>
          <div class="sm-inline-edit-form">
            <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=edit_entity" class="sm-form-row">
              <?= sm_csrf_field() ?>
              <input type="hidden" name="entity_type" value="task">
              <input type="hidden" name="entity_id" value="<?= (int)$task['id'] ?>">
              <label>Nimi <input name="name" required value="<?= sm_h($task['name']) ?>"></label>
              <label>Tyyppi
                <select name="work_type">
                  <option value="task" <?= $wt === 'task' ? 'selected' : '' ?>>Tehtävä</option>
                  <option value="work_type" <?= $wt === 'work_type' ? 'selected' : '' ?>>Työlaji</option>
                  <option value="position" <?= $wt === 'position' ? 'selected' : '' ?>>Vakanssi</option>
                </select>
              </label>
              <label>Kategoria <input name="category" required value="<?= sm_h($task['category']) ?>"></label>
              <div style="display:flex;gap:.5rem;align-items:center">
                <button class="sm-btn sm-btn-primary sm-btn-sm" type="submit">Tallenna</button>
                <a href="<?= sm_h(sm_base_url()) ?>/index.php?page=dashboard&tab=tasks" class="sm-btn sm-btn-ghost sm-btn-sm">Peruuta</a>
              </div>
            </form>
          </div>
          <?php endif; ?>
        <?php endforeach; ?>
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

<?php /* ====== PPE LIBRARY ====== */ ?>
<?php elseif ($activeTab === 'ppe'): ?>
<section aria-labelledby="tab-ppe-heading">
  <div class="sm-section-header">
    <h2 class="sm-section-title" id="tab-ppe-heading"><?= sm_h(sm_t('tab_ppe', $lang)) ?></h2>
  </div>
  <div class="sm-card" style="padding:0">
    <?php if (empty($data['ppeItems'])): ?>
      <div class="sm-empty" style="margin:1.5rem"><p>Suojainkirjasto on tyhjä.</p></div>
    <?php else: ?>
      <div class="sm-list-block" style="border:0;border-radius:0">
        <?php foreach ($data['ppeItems'] as $item):
          $cls = (string)($item['item_class'] ?? 'personal_protection');
          $isEditing = ($editId === (int)$item['id'] && $activeTab === 'ppe');
          $imgUrl = sm_ppe_img_url($item);
        ?>
          <div class="sm-list-row">
            <img src="<?= sm_h($imgUrl) ?>" alt="" width="36" height="36" class="sm-list-row-img" style="object-fit:contain;border-radius:6px;background:var(--sm-surface-2)">
            <div class="sm-list-row-main">
              <div class="sm-list-row-name"><?= sm_h($item['name']) ?> <code class="sm-code-pill"><?= sm_h($item['code']) ?></code></div>
              <div class="sm-list-row-meta">
                <span class="sm-badge <?= $cls === 'other_safety' ? 'sm-badge-global' : 'sm-badge-mandatory' ?>" style="font-size:.72rem"><?= sm_h(sm_t($cls, $lang)) ?></span>
                <?php if (!empty($item['standard_ref'])): ?><span style="color:var(--sm-muted);font-size:.78rem;font-family:monospace"> <?= sm_h((string)$item['standard_ref']) ?></span><?php endif; ?>
              </div>
            </div>
            <div class="sm-list-row-actions">
              <a href="<?= sm_h(sm_base_url()) ?>/index.php?page=dashboard&tab=ppe&edit_id=<?= (int)$item['id'] ?>"
                 class="sm-btn sm-btn-ghost sm-btn-sm"><?= $iconEdit ?> Muokkaa</a>
              <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=archive_entity"
                    data-confirm="<?= sm_h('Arkistoi suojain ' . $item['name'] . '?') ?>" onsubmit="return confirm(this.dataset.confirm)">
                <?= sm_csrf_field() ?>
                <input type="hidden" name="entity_type" value="ppe">
                <input type="hidden" name="entity_id" value="<?= (int)$item['id'] ?>">
                <button class="sm-btn sm-btn-ghost sm-btn-sm sm-btn-danger-ghost" title="Arkistoi"><?= $iconArchive ?></button>
              </form>
            </div>
          </div>
          <?php if ($isEditing): ?>
          <div class="sm-inline-edit-form">
            <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=edit_entity" class="sm-rule-form-grid">
              <?= sm_csrf_field() ?>
              <input type="hidden" name="entity_type" value="ppe">
              <input type="hidden" name="entity_id" value="<?= (int)$item['id'] ?>">
              <label>Nimi <input name="name" required value="<?= sm_h($item['name']) ?>"></label>
              <label>Luokka
                <select name="item_class">
                  <option value="personal_protection" <?= $cls === 'personal_protection' ? 'selected' : '' ?>><?= sm_h(sm_t('personal_protection', $lang)) ?></option>
                  <option value="other_safety" <?= $cls === 'other_safety' ? 'selected' : '' ?>><?= sm_h(sm_t('other_safety', $lang)) ?></option>
                </select>
              </label>
              <label>Kategoria <input name="category" required value="<?= sm_h($item['category']) ?>"></label>
              <label>Standardi <input name="standard_ref" value="<?= sm_h((string)($item['standard_ref'] ?? '')) ?>"></label>
              <label>Ikoni <input name="icon" required value="<?= sm_h($item['icon']) ?>"></label>
              <div style="display:flex;gap:.5rem;align-items:center">
                <button class="sm-btn sm-btn-primary sm-btn-sm" type="submit">Tallenna</button>
                <a href="<?= sm_h(sm_base_url()) ?>/index.php?page=dashboard&tab=ppe" class="sm-btn sm-btn-ghost sm-btn-sm">Peruuta</a>
              </div>
            </form>
            <div class="sm-ppe-img-upload">
              <strong>Kuva suojaimelle</strong>
              <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=upload_ppe_image"
                    enctype="multipart/form-data" class="sm-inline-form" style="margin-top:.5rem">
                <?= sm_csrf_field() ?>
                <input type="hidden" name="ppe_item_id" value="<?= (int)$item['id'] ?>">
                <input type="file" name="ppe_image" accept="image/svg+xml,image/png,image/jpeg,image/webp" class="sm-file-input">
                <button class="sm-btn sm-btn-secondary sm-btn-sm" type="submit">Lataa kuva</button>
              </form>
              <p class="sm-hint">Sallittu: SVG, PNG, JPG, WEBP – max 2 MB</p>
            </div>
          </div>
          <?php endif; ?>
        <?php endforeach; ?>
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

<?php /* ====== RULES ====== */ ?>
<?php elseif ($activeTab === 'rules'): ?>
<section aria-labelledby="tab-rules-heading">
  <div class="sm-section-header">
    <h2 class="sm-section-title" id="tab-rules-heading"><?= sm_h(sm_t('tab_rules', $lang)) ?></h2>
  </div>

  <?php if (!empty($data['rules'])): ?>
  <div class="sm-card" style="padding:0;overflow:hidden;margin-bottom:1.25rem;">
    <div class="sm-table-wrap">
      <table class="sm-table"><thead><tr>
        <th>Taso</th><th>Kohde</th><th>Suojain</th><th>Vaatimus</th><th>Tila</th><th>Ver.</th><th style="min-width:160px">Toiminnot</th>
      </tr></thead><tbody>
      <?php foreach ($data['rules'] as $rule):
        $scopeKey  = (string)$rule['scope_type'];
        $levelKey  = (string)$rule['requirement_level'];
        $statusKey = (string)$rule['status'];
        $scopeMap  = ['global'=>'Yleinen','environment'=>'Ympäristö','site'=>'Työmaa','zone'=>'Alue',
                      'task'=>'Tehtävä','site_task'=>'Työmaa+tehtävä','local'=>'Paikallinen',
                      'zone_task'=>'Alue+tehtävä','phase'=>'Työvaihe','exception'=>'Poikkeus'];
        $isEditingRule = ($editId === (int)$rule['id'] && $activeTab === 'rules');
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
            <div style="display:flex;flex-wrap:wrap;gap:.3rem;align-items:center">
              <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=change_rule_status" class="sm-rule-status-form">
                <?= sm_csrf_field() ?>
                <input type="hidden" name="rule_id" value="<?= (int)$rule['id'] ?>">
                <select name="status" aria-label="Tila">
                  <?php foreach (['draft'=>'Luonnos','review'=>'Tarkastettavana','approved'=>'Hyväksytty','published'=>'Julkaistu','archived'=>'Arkistoitu'] as $v=>$l): ?>
                    <option value="<?= sm_h($v) ?>" <?= $statusKey === $v ? 'selected' : '' ?>><?= sm_h($l) ?></option>
                  <?php endforeach; ?>
                </select>
                <button class="sm-btn sm-btn-secondary sm-btn-sm" type="submit">Siirrä</button>
              </form>
              <a href="<?= sm_h(sm_base_url()) ?>/index.php?page=dashboard&tab=rules&edit_id=<?= (int)$rule['id'] ?>"
                 class="sm-btn sm-btn-ghost sm-btn-sm"><?= $iconEdit ?></a>
              <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=archive_rule"
                    data-confirm="<?= sm_h(sm_t('confirm_archive_rule', $lang)) ?>" onsubmit="return confirm(this.dataset.confirm)">
                <?= sm_csrf_field() ?>
                <input type="hidden" name="rule_id" value="<?= (int)$rule['id'] ?>">
                <button class="sm-btn sm-btn-ghost sm-btn-sm sm-btn-danger-ghost" title="Arkistoi"><?= $iconArchive ?></button>
              </form>
            </div>
            <?php if ($isEditingRule): ?>
            <div class="sm-inline-edit-form" style="margin-top:.75rem;width:100%;min-width:340px">
              <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=edit_rule" class="sm-rule-form-grid">
                <?= sm_csrf_field() ?>
                <input type="hidden" name="rule_id" value="<?= (int)$rule['id'] ?>">
                <label>Scope-taso
                  <select name="scope_type">
                    <?php foreach (['global'=>'Yleinen','environment'=>'Ympäristö','site'=>'Työmaa','zone'=>'Alue','task'=>'Tehtävä','site_task'=>'Työmaa+työlaji','zone_task'=>'Alue+työlaji','phase'=>'Työvaihe','exception'=>'Poikkeus'] as $sv=>$sl): ?>
                      <option value="<?= sm_h($sv) ?>" <?= $scopeKey === $sv ? 'selected' : '' ?>><?= sm_h($sl) ?></option>
                    <?php endforeach; ?>
                  </select>
                </label>
                <label>Toimintaympäristö
                  <select name="environment_id">
                    <option value="0">— ei rajausta —</option>
                    <?php foreach ($data['environments'] as $env): ?>
                      <option value="<?= (int)$env['id'] ?>" <?= (int)($rule['environment_id'] ?? 0) === (int)$env['id'] ? 'selected' : '' ?>><?= sm_h($env['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </label>
                <label>Työmaa
                  <select name="site_id">
                    <option value="0">— ei rajausta —</option>
                    <?php foreach ($data['sites'] as $site): ?>
                      <option value="<?= (int)$site['id'] ?>" <?= (int)($rule['site_id'] ?? 0) === (int)$site['id'] ? 'selected' : '' ?>><?= sm_h($site['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </label>
                <label>Alue
                  <select name="zone_id">
                    <option value="0">— ei rajausta —</option>
                    <?php foreach ($data['zones'] as $zone): ?>
                      <option value="<?= (int)$zone['id'] ?>" <?= (int)($rule['zone_id'] ?? 0) === (int)$zone['id'] ? 'selected' : '' ?>><?= sm_h($zone['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </label>
                <label>Työlaji
                  <select name="task_id">
                    <option value="0">— ei rajausta —</option>
                    <?php foreach ($data['tasks'] as $task): ?>
                      <option value="<?= (int)$task['id'] ?>" <?= (int)($rule['task_id'] ?? 0) === (int)$task['id'] ? 'selected' : '' ?>><?= sm_h($task['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </label>
                <label>Suojain
                  <select name="ppe_item_id">
                    <?php foreach ($data['ppeItems'] as $ppe): ?>
                      <option value="<?= (int)$ppe['id'] ?>" <?= (int)$rule['ppe_item_id'] === (int)$ppe['id'] ? 'selected' : '' ?>><?= sm_h($ppe['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </label>
                <label>Vaatimustaso
                  <select name="requirement_level">
                    <?php foreach (['mandatory'=>sm_t('mandatory',$lang),'conditional'=>sm_t('conditional',$lang),'recommended'=>sm_t('recommended',$lang),'information'=>sm_t('information',$lang),'not_applicable'=>sm_t('not_applicable',$lang),'prohibited'=>sm_t('prohibited',$lang)] as $rv=>$rl): ?>
                      <option value="<?= sm_h($rv) ?>" <?= $levelKey === $rv ? 'selected' : '' ?>><?= sm_h($rl) ?></option>
                    <?php endforeach; ?>
                  </select>
                </label>
                <label class="sm-form-full">Ehto <input name="condition_text" value="<?= sm_h((string)($rule['condition_text'] ?? '')) ?>"></label>
                <label class="sm-form-full">Huomio <input name="notes" value="<?= sm_h((string)($rule['notes'] ?? '')) ?>"></label>
                <label class="sm-form-full">Muutoskuvaus <input name="change_description" value=""></label>
                <div class="sm-form-full" style="display:flex;gap:.5rem">
                  <button class="sm-btn sm-btn-primary sm-btn-sm" type="submit">Tallenna muutos</button>
                  <a href="<?= sm_h(sm_base_url()) ?>/index.php?page=dashboard&tab=rules" class="sm-btn sm-btn-ghost sm-btn-sm">Peruuta</a>
                </div>
              </form>
            </div>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody></table>
    </div>
  </div>
  <?php else: ?>
    <div class="sm-empty"><p>Vaatimussääntöjä ei ole vielä lisätty.</p></div>
  <?php endif; ?>

  <!-- Lisää uusi yksittäinen sääntö -->
  <div class="sm-card sm-add-form-block" style="border-style:dashed;background:var(--sm-surface-2)">
    <h3><?= sm_h(sm_t('add_single_rule', $lang)) ?></h3>
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
      <label class="sm-form-full">Ehto <input name="condition_text" autocomplete="off" placeholder="Milloin aktivoituu"></label>
      <label class="sm-form-full">Huomio <input name="notes" autocomplete="off" placeholder="Lisätiedot tai perustelu"></label>
      <label class="sm-form-full">Muutoskuvaus <input name="change_description" autocomplete="off" placeholder="Muutos versiointia varten"></label>
      <div class="sm-form-full"><button class="sm-btn sm-btn-primary" type="submit">Tallenna sääntö</button></div>
    </form>
  </div>

  <!-- Massalisäys -->
  <div class="sm-card" style="margin-top:1.25rem;border:2px solid var(--sm-accent);background:var(--sm-surface-2)">
    <div class="sm-section-header" style="margin-bottom:.75rem">
      <h3 class="sm-section-title">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" style="vertical-align:middle"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <?= sm_h(sm_t('bulk_add_title', $lang)) ?>
      </h3>
    </div>
    <p style="font-size:.85rem;color:var(--sm-muted);margin:0 0 1rem">
      <?= sm_h(sm_t('bulk_add_desc', $lang)) ?>
    </p>
    <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=bulk_add_rule"
          class="sm-rule-form-grid" id="sm-bulk-form"
          data-confirm="<?= sm_h(sm_t('bulk_add_confirm', $lang)) ?>"
          onsubmit="return confirm(this.dataset.confirm)">
      <?= sm_csrf_field() ?>
      <label>Suojain
        <select name="ppe_item_id" required>
          <option value="0">— valitse suojain —</option>
          <?php foreach ($data['ppeItems'] as $item): ?>
            <option value="<?= (int)$item['id'] ?>"><?= sm_h($item['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Vaatimustaso
        <select name="requirement_level">
          <option value="mandatory">Pakollinen</option>
          <option value="conditional">Tilanteen mukaan</option>
          <option value="recommended">Suositeltava</option>
        </select>
      </label>
      <label>Toimintaympäristö (rajaus)
        <select name="environment_id">
          <option value="0">— kaikki —</option>
          <?php foreach ($data['environments'] as $env): ?>
            <option value="<?= (int)$env['id'] ?>"><?= sm_h($env['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Työmaa (rajaus)
        <select name="site_id">
          <option value="0">— kaikki työmaat —</option>
          <?php foreach ($data['sites'] as $site): ?>
            <option value="<?= (int)$site['id'] ?>"><?= sm_h($site['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label class="sm-form-full">Ehto <input name="condition_text" autocomplete="off" placeholder="Esim. pölyisessä työssä"></label>
      <label class="sm-form-full">Huomio <input name="notes" autocomplete="off" placeholder="Lisätiedot"></label>
      <div class="sm-form-full" style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap">
        <button class="sm-btn sm-btn-primary" type="submit">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg>
          Lisää kaikille tehtäville (luonnos)
        </button>
        <span class="sm-hint" style="font-size:.8rem;color:var(--sm-muted)">Luodaan draft-säännöt, jotka pitää erikseen julkaista.</span>
      </div>
    </form>
  </div>
</section>

<?php /* ====== AUDIT ====== */ ?>
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
