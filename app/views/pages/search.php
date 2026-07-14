<?php
$lang      = $_SESSION['sm_lang'] ?? 'fi';
$selEnv    = (int)($_GET['env_id']  ?? 0);
$selSite   = (int)($_GET['site_id'] ?? 0);
$selZone   = (int)($_GET['zone_id'] ?? 0);
$selTask   = (int)($_GET['task_id'] ?? 0);
$hasSearch = ($selSite > 0 || $selEnv > 0) && $selTask > 0;

// Etsi valitut nimet kontekstia varten
$ctxEnv  = $result['context']['env']  ?? null;
$ctxSite = $result['context']['site'] ?? null;
$ctxZone = $result['context']['zone'] ?? null;
$ctxTask = $result['context']['task'] ?? null;
?>

<div class="sm-page-header">
  <div class="sm-page-header-left">
    <h1><?= sm_h(sm_t('nav_search', $lang)) ?></h1>
    <p class="sm-page-subtitle"><?= sm_h(sm_t('select_environment', $lang)) ?> &rsaquo; <?= sm_h(sm_t('select_site', $lang)) ?> &rsaquo; <?= sm_h(sm_t('select_task', $lang)) ?></p>
  </div>
</div>

<div class="sm-card" id="sm-search-form-card">
  <form method="get" action="<?= sm_h(sm_base_url()) ?>/index.php" role="search" id="sm-search-form">
    <input type="hidden" name="page" value="search">

    <div class="sm-search-steps">

      <!-- Vaihe 1: Toimintaympäristö -->
      <div class="sm-search-step">
        <label class="sm-step-label" for="sm-env-select">
          <span class="sm-step-num">1</span>
          <?= sm_h(sm_t('select_environment', $lang)) ?>
        </label>
        <select id="sm-env-select" name="env_id" class="sm-step-select" autocomplete="off">
          <option value="0"><?= sm_h(sm_t('all_environments', $lang)) ?></option>
          <?php foreach ($environments as $env): ?>
            <option value="<?= (int)$env['id'] ?>" <?= $selEnv === (int)$env['id'] ? 'selected' : '' ?>>
              <?= sm_h($env['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Vaihe 2: Työmaa / toimipaikka -->
      <div class="sm-search-step">
        <label class="sm-step-label" for="sm-site-select">
          <span class="sm-step-num">2</span>
          <?= sm_h(sm_t('select_site', $lang)) ?>
        </label>
        <select id="sm-site-select" name="site_id" class="sm-step-select" autocomplete="off">
          <option value="0"><?= sm_h(sm_t('all_sites', $lang)) ?></option>
          <?php foreach ($sites as $site): ?>
            <option value="<?= (int)$site['id'] ?>" <?= $selSite === (int)$site['id'] ? 'selected' : '' ?>>
              <?= sm_h($site['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Vaihe 3: Alue / laitos (piilotetaan jos ei alueita) -->
      <div class="sm-search-step <?= empty($zones) ? 'sm-step-hidden' : '' ?>" id="sm-zone-step">
        <label class="sm-step-label" for="sm-zone-select">
          <span class="sm-step-num">3</span>
          <?= sm_h(sm_t('select_zone', $lang)) ?>
        </label>
        <select id="sm-zone-select" name="zone_id" class="sm-step-select" autocomplete="off">
          <option value="0"><?= sm_h(sm_t('no_zone', $lang)) ?></option>
          <?php foreach ($zones as $zone): ?>
            <option value="<?= (int)$zone['id'] ?>" <?= $selZone === (int)$zone['id'] ? 'selected' : '' ?>>
              <?= sm_h($zone['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Vaihe 4: Työlaji / tehtävä / vakanssi -->
      <div class="sm-search-step">
        <label class="sm-step-label" for="sm-task-select">
          <span class="sm-step-num"><?= empty($zones) ? '3' : '4' ?></span>
          <?= sm_h(sm_t('select_task', $lang)) ?>
        </label>
        <select id="sm-task-select" name="task_id" class="sm-step-select" autocomplete="off">
          <option value="0"><?= sm_h(sm_t('all_tasks', $lang)) ?></option>
          <?php foreach ($tasks as $task): ?>
            <option value="<?= (int)$task['id'] ?>" <?= $selTask === (int)$task['id'] ? 'selected' : '' ?>>
              <?= sm_h($task['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

    </div><!-- /.sm-search-steps -->

    <div class="sm-search-actions">
      <button class="sm-btn sm-btn-primary sm-btn-large" type="submit">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <?= sm_h(sm_t('show_requirements', $lang)) ?>
      </button>
    </div>
  </form>
</div>

<?php if ($hasSearch && $result !== null): ?>

<!-- Otsikkokortti (kontekstiyhteenveto) -->
<div class="sm-result-header-card">
  <div class="sm-result-context">
    <?php if ($ctxEnv): ?>
      <div class="sm-result-context-row">
        <span class="sm-context-label"><?= sm_h(sm_t('environment', $lang)) ?></span>
        <span class="sm-context-value"><?= sm_h((string)$ctxEnv['name']) ?></span>
      </div>
    <?php endif; ?>
    <?php if ($ctxSite): ?>
      <div class="sm-result-context-row">
        <span class="sm-context-label"><?= sm_h(sm_t('select_site', $lang)) ?></span>
        <span class="sm-context-value"><?= sm_h((string)$ctxSite['name']) ?></span>
      </div>
    <?php endif; ?>
    <?php if ($ctxZone): ?>
      <div class="sm-result-context-row">
        <span class="sm-context-label"><?= sm_h(sm_t('zone', $lang)) ?></span>
        <span class="sm-context-value"><?= sm_h((string)$ctxZone['name']) ?></span>
      </div>
    <?php endif; ?>
    <?php if ($ctxTask): ?>
      <div class="sm-result-context-row">
        <span class="sm-context-label"><?= sm_h(sm_t('select_task', $lang)) ?></span>
        <span class="sm-context-value sm-context-task"><?= sm_h((string)$ctxTask['name']) ?></span>
      </div>
    <?php endif; ?>
  </div>
  <div class="sm-official-notice" role="note">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
    <?= sm_h(sm_t('official_only', $lang)) ?>
  </div>
</div>

<?php
$sections  = $result['sections'];
$conflicts = $result['conflicts'];
$hasSections = !empty($sections['always'])
    || !empty($sections['conditional'])
    || !empty($sections['other_safety'])
    || !empty($sections['information']);
?>

<?php if (!$hasSections): ?>
  <div class="sm-empty" style="margin-top:1.5rem">
    <div class="sm-empty-icon" aria-hidden="true">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
    </div>
    <p>Ei suojainsääntöjä tälle valinnalle. Tarkista asetukset hallintapaneelista.</p>
  </div>
<?php endif; ?>

<!-- A) Aina vaadittavat -->
<?php if (!empty($sections['always'])): ?>
<section class="sm-result-section" aria-labelledby="sec-always-heading">
  <div class="sm-section-header">
    <h2 class="sm-section-title" id="sec-always-heading">
      <span class="sm-section-badge sm-section-badge-always">A</span>
      <?= sm_h(sm_t('section_always', $lang)) ?>
    </h2>
    <span class="sm-badge sm-badge-mandatory"><?= count($sections['always']) ?></span>
  </div>
  <div class="sm-grid-cards">
    <?php foreach ($sections['always'] as $card): ?>
      <?= sm_render_ppe_card($card, $lang) ?>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- B) Tilanteen mukaan -->
<?php if (!empty($sections['conditional'])): ?>
<section class="sm-result-section" aria-labelledby="sec-cond-heading">
  <div class="sm-section-header">
    <h2 class="sm-section-title" id="sec-cond-heading">
      <span class="sm-section-badge sm-section-badge-conditional">B</span>
      <?= sm_h(sm_t('section_conditional', $lang)) ?>
    </h2>
    <span class="sm-badge sm-badge-conditional"><?= count($sections['conditional']) ?></span>
  </div>
  <div class="sm-grid-cards">
    <?php foreach ($sections['conditional'] as $card): ?>
      <?= sm_render_ppe_card($card, $lang) ?>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- C) Muut turvallisuusvarusteet -->
<?php if (!empty($sections['other_safety'])): ?>
<section class="sm-result-section" aria-labelledby="sec-other-heading">
  <div class="sm-section-header">
    <h2 class="sm-section-title" id="sec-other-heading">
      <span class="sm-section-badge sm-section-badge-other">C</span>
      <?= sm_h(sm_t('section_other', $lang)) ?>
    </h2>
    <span class="sm-badge sm-badge-published"><?= count($sections['other_safety']) ?></span>
  </div>
  <div class="sm-grid-cards">
    <?php foreach ($sections['other_safety'] as $card): ?>
      <?= sm_render_ppe_card($card, $lang) ?>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- D) Kriittiset huomiot -->
<?php if (!empty($sections['information'])): ?>
<section class="sm-result-section" aria-labelledby="sec-info-heading">
  <div class="sm-section-header">
    <h2 class="sm-section-title" id="sec-info-heading">
      <span class="sm-section-badge sm-section-badge-info">D</span>
      <?= sm_h(sm_t('section_critical', $lang)) ?>
    </h2>
  </div>
  <div class="sm-info-list">
    <?php foreach ($sections['information'] as $card): ?>
      <div class="sm-info-row">
        <strong><?= sm_h((string)$card['ppe']['name']) ?></strong>
        <?php if (!empty($card['rule']['notes'])): ?>
          <p><?= sm_h((string)$card['rule']['notes']) ?></p>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- E) Ohjeet ja liitteet (placeholder) -->
<section class="sm-result-section" aria-labelledby="sec-attach-heading">
  <div class="sm-section-header">
    <h2 class="sm-section-title" id="sec-attach-heading">
      <span class="sm-section-badge sm-section-badge-attach">E</span>
      <?= sm_h(sm_t('section_attachments', $lang)) ?>
    </h2>
  </div>
  <div class="sm-empty sm-empty-compact">
    <p style="color:var(--sm-muted);font-size:.9rem">Ei liitteitä tälle valinnalle.</p>
  </div>
</section>

<!-- Ristiriidat (vain ylläpitäjille) -->
<?php
$userRole = sm_current_user()['role_slug'] ?? '';
$isAdmin  = in_array($userRole, ['admin','hseq_reviewer','hseq_approver','site_manager'], true);
if ($isAdmin && !empty($conflicts)): ?>
<section class="sm-card sm-card-warning" style="margin-top:1.25rem" aria-label="Ristiriidat">
  <div class="sm-section-header">
    <h2 class="sm-section-title">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
      Ristiriidat
    </h2>
    <span class="sm-badge sm-badge-review"><?= count($conflicts) ?> ohitettu</span>
  </div>
  <div class="sm-list-block">
    <?php foreach ($conflicts as $conflict): ?>
      <div class="sm-list-row">
        <div class="sm-list-row-main">
          <div class="sm-list-row-name">Suojain #<?= (int)$conflict['ppe_item_id'] ?></div>
          <div class="sm-list-row-meta">
            Ohitettu sääntö: <?= sm_h((string)$conflict['discarded']['scope_type']) ?>
            &rarr; <?= sm_h((string)$conflict['reason']) ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<div class="sm-print-actions">
  <button onclick="window.print()" class="sm-btn sm-btn-secondary">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
    <?= sm_h(sm_t('print', $lang)) ?>
  </button>
</div>

<?php endif; ?>

<script type="module" src="<?= sm_h(sm_base_url()) ?>/assets/js/modules/search.js"></script>
