<?php
$lang      = $_SESSION['sm_lang'] ?? 'fi';
$selEnv    = (int)($_GET['env_id']  ?? 0);
$selSite   = (int)($_GET['site_id'] ?? 0);
$selZone   = (int)($_GET['zone_id'] ?? 0);
$selTask   = (int)($_GET['task_id'] ?? 0);
$filtersReady = ($selEnv > 0 || $selSite > 0);
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
      <div class="sm-search-step">
        <label class="sm-step-label" for="sm-env-select">
          <span class="sm-step-num">1</span>
          <?= sm_h(sm_t('select_environment', $lang)) ?>
        </label>
        <select id="sm-env-select" name="env_id" class="sm-step-select" autocomplete="off">
          <option value="0"><?= sm_h(sm_t('all_environments', $lang)) ?></option>
          <?php foreach ($environments as $env): ?>
            <option value="<?= (int)$env['id'] ?>" <?= $selEnv === (int)$env['id'] ? 'selected' : '' ?>>
              <?= sm_h((string)$env['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="sm-search-step">
        <label class="sm-step-label" for="sm-site-select">
          <span class="sm-step-num">2</span>
          <?= sm_h(sm_t('select_site', $lang)) ?>
        </label>
        <select id="sm-site-select" name="site_id" class="sm-step-select" autocomplete="off">
          <option value="0"><?= sm_h(sm_t('all_sites', $lang)) ?></option>
          <?php foreach ($sites as $site): ?>
            <option value="<?= (int)$site['id'] ?>" <?= $selSite === (int)$site['id'] ? 'selected' : '' ?>>
              <?= sm_h((string)$site['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="sm-search-step <?= empty($zones) ? 'sm-step-hidden' : '' ?>" id="sm-zone-step">
        <label class="sm-step-label" for="sm-zone-select">
          <span class="sm-step-num">3</span>
          <?= sm_h(sm_t('select_zone', $lang)) ?>
        </label>
        <select id="sm-zone-select" name="zone_id" class="sm-step-select" autocomplete="off">
          <option value="0"><?= sm_h(sm_t('no_zone', $lang)) ?></option>
          <?php foreach ($zones as $zone): ?>
            <option value="<?= (int)$zone['id'] ?>" <?= $selZone === (int)$zone['id'] ? 'selected' : '' ?>>
              <?= sm_h((string)$zone['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
  </form>
</div>

<section class="sm-result-section" aria-labelledby="task-list-heading">
  <div class="sm-section-header">
    <h2 class="sm-section-title" id="task-list-heading"><?= sm_h(sm_t('task_cards', $lang)) ?></h2>
    <span class="sm-badge sm-badge-published"><?= count($taskCards) ?></span>
  </div>

  <?php if (!$filtersReady): ?>
    <div class="sm-empty">
      <p><?= sm_h(sm_t('search_select_filters_hint', $lang)) ?></p>
    </div>
  <?php elseif (empty($taskCards)): ?>
    <div class="sm-empty">
      <p><?= sm_h(sm_t('search_no_tasks', $lang)) ?></p>
    </div>
  <?php else: ?>
    <div class="sm-task-card-grid">
      <?php foreach ($taskCards as $card):
        $task = $card['task'];
        $taskId = (int)$task['id'];
        $summary = $card['summary'];
        $ctx = $card['context'];
      ?>
      <article class="sm-task-card" data-task-id="<?= $taskId ?>">
        <div class="sm-task-card-head">
          <h3><?= sm_h((string)$task['name']) ?></h3>
          <span class="sm-badge sm-badge-global"><?= sm_h((string)($task['work_type'] ?? 'task')) ?></span>
        </div>
        <?php if (!empty($task['description'])): ?>
          <p class="sm-task-card-desc"><?= sm_h((string)$task['description']) ?></p>
        <?php else: ?>
          <p class="sm-task-card-desc sm-text-muted"><?= sm_h(sm_t('no_description', $lang)) ?></p>
        <?php endif; ?>

        <div class="sm-task-card-meta">
          <?php if (!empty($ctx['env']['name'])): ?><span class="sm-context-chip"><?= sm_h((string)$ctx['env']['name']) ?></span><?php endif; ?>
          <?php if (!empty($ctx['site']['name'])): ?><span class="sm-context-chip"><?= sm_h((string)$ctx['site']['name']) ?></span><?php endif; ?>
          <?php if (!empty($ctx['zone']['name'])): ?><span class="sm-context-chip"><?= sm_h((string)$ctx['zone']['name']) ?></span><?php endif; ?>
        </div>

        <div class="sm-task-card-summary">
          <span><?= sm_h(sm_tr('count_mandatory', ['count' => (int)$summary['always']], $lang)) ?></span>
          <span><?= sm_h(sm_tr('count_conditional', ['count' => (int)$summary['conditional']], $lang)) ?></span>
          <span><?= sm_h(sm_tr('count_other', ['count' => (int)$summary['other']], $lang)) ?></span>
        </div>

        <button class="sm-btn sm-btn-primary sm-btn-sm" type="button" data-open-task-modal="<?= $taskId ?>">
          <?= sm_h(sm_t('open_task', $lang)) ?>
        </button>
      </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<?php foreach ($taskCards as $card):
  $task = $card['task'];
  $taskId = (int)$task['id'];
  $sections = $card['sections'];
  $ctx = $card['context'];
  $nAlways      = count($sections['always'] ?? []);
  $nConditional = count($sections['conditional'] ?? []);
  $nOther       = count($sections['other_safety'] ?? []);
  $nNotes       = count($card['notes'] ?? []);
  $nRisks       = count($card['risks'] ?? []);
?>
<dialog class="sm-modal sm-task-modal" id="sm-task-modal-<?= $taskId ?>" aria-labelledby="sm-task-modal-title-<?= $taskId ?>">
  <div class="sm-modal-header">
    <h3 id="sm-task-modal-title-<?= $taskId ?>"><?= sm_h((string)$task['name']) ?></h3>
    <button class="sm-btn sm-btn-ghost sm-btn-sm" type="button" data-close-task-modal>&times;</button>
  </div>

  <div class="sm-modal-path">
    <?php if (!empty($ctx['env']['name'])): ?><span><?= sm_h((string)$ctx['env']['name']) ?></span><?php endif; ?>
    <?php if (!empty($ctx['site']['name'])): ?><span><?= sm_h((string)$ctx['site']['name']) ?></span><?php endif; ?>
    <?php if (!empty($ctx['zone']['name'])): ?><span><?= sm_h((string)$ctx['zone']['name']) ?></span><?php endif; ?>
  </div>

  <?php if (!empty($task['description'])): ?>
    <p class="sm-modal-description"><?= sm_h((string)$task['description']) ?></p>
  <?php endif; ?>

  <?php /* ── Yhteenveto ── */ ?>
  <div class="sm-modal-summary" role="region" aria-label="<?= sm_h(sm_t('summary', $lang)) ?>">
    <?php if ($nAlways > 0): ?>
    <div class="sm-modal-stat sm-modal-stat-mandatory">
      <span class="sm-modal-stat-value"><?= $nAlways ?></span>
      <span class="sm-modal-stat-label"><?= sm_h(sm_t('section_always', $lang)) ?></span>
    </div>
    <?php endif; ?>
    <?php if ($nConditional > 0): ?>
    <div class="sm-modal-stat sm-modal-stat-conditional">
      <span class="sm-modal-stat-value"><?= $nConditional ?></span>
      <span class="sm-modal-stat-label"><?= sm_h(sm_t('section_conditional', $lang)) ?></span>
    </div>
    <?php endif; ?>
    <?php if ($nOther > 0): ?>
    <div class="sm-modal-stat sm-modal-stat-other">
      <span class="sm-modal-stat-value"><?= $nOther ?></span>
      <span class="sm-modal-stat-label"><?= sm_h(sm_t('section_other', $lang)) ?></span>
    </div>
    <?php endif; ?>
    <?php if ($nNotes + $nRisks > 0): ?>
    <div class="sm-modal-stat sm-modal-stat-notes">
      <span class="sm-modal-stat-value"><?= $nNotes + $nRisks ?></span>
      <span class="sm-modal-stat-label"><?= sm_h(sm_t('notes_and_risks', $lang)) ?></span>
    </div>
    <?php endif; ?>
  </div>

  <?php if (!empty($sections['always'])): ?>
    <section class="sm-result-section">
      <div class="sm-section-header">
        <h4 class="sm-section-title">
          <span class="sm-section-badge sm-section-badge-always">!</span>
          <?= sm_h(sm_t('section_always', $lang)) ?>
        </h4>
        <span class="sm-badge sm-badge-mandatory"><?= $nAlways ?></span>
      </div>
      <div class="sm-ppe-grid">
        <?php foreach ($sections['always'] as $ppeCard): ?>
          <?= sm_render_ppe_card($ppeCard, $lang) ?>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

  <?php if (!empty($sections['conditional'])): ?>
    <section class="sm-result-section">
      <div class="sm-section-header">
        <h4 class="sm-section-title">
          <span class="sm-section-badge sm-section-badge-conditional">?</span>
          <?= sm_h(sm_t('section_conditional', $lang)) ?>
        </h4>
        <span class="sm-badge sm-badge-conditional"><?= $nConditional ?></span>
      </div>
      <div class="sm-ppe-grid">
        <?php foreach ($sections['conditional'] as $ppeCard): ?>
          <?= sm_render_ppe_card($ppeCard, $lang) ?>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

  <?php if (!empty($sections['other_safety'])): ?>
    <section class="sm-result-section">
      <div class="sm-section-header">
        <h4 class="sm-section-title">
          <span class="sm-section-badge sm-section-badge-other">+</span>
          <?= sm_h(sm_t('section_other', $lang)) ?>
        </h4>
        <span class="sm-badge sm-badge-published"><?= $nOther ?></span>
      </div>
      <div class="sm-ppe-grid">
        <?php foreach ($sections['other_safety'] as $ppeCard): ?>
          <?= sm_render_ppe_card($ppeCard, $lang) ?>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

  <?php if (!empty($card['notes']) || !empty($card['risks'])): ?>
    <section class="sm-result-section">
      <div class="sm-section-header">
        <h4 class="sm-section-title"><?= sm_h(sm_t('notes_and_risks', $lang)) ?></h4>
      </div>
      <div class="sm-info-list">
        <?php foreach ($card['notes'] as $note): ?><div class="sm-info-row"><p><?= sm_h((string)$note) ?></p></div><?php endforeach; ?>
        <?php foreach ($card['risks'] as $risk): ?><div class="sm-info-row sm-info-row-risk"><strong><?= sm_h(sm_t('risk', $lang)) ?></strong><p><?= sm_h((string)$risk) ?></p></div><?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>
</dialog>
<?php endforeach; ?>

<script type="module" src="<?= sm_h(sm_base_url()) ?>/assets/js/modules/search.js"></script>
