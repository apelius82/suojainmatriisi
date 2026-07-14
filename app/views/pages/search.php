<?php
$lang      = $_SESSION['sm_lang'] ?? 'fi';
$selEnv    = (int)($_GET['env_id']  ?? 0);
$selSite   = (int)($_GET['site_id'] ?? 0);
$selZone   = (int)($_GET['zone_id'] ?? 0);
$isAdmin   = sm_is_admin();
$hasFilter = ($selEnv > 0 || $selSite > 0);
?>

<div class="sm-page-header">
  <div class="sm-page-header-left">
    <h1><?= sm_h(sm_t('nav_search', $lang)) ?></h1>
    <p class="sm-page-subtitle"><?= sm_h(sm_t('select_environment', $lang)) ?> &rsaquo; <?= sm_h(sm_t('select_site', $lang)) ?> &rsaquo; <?= sm_h(sm_t('select_zone', $lang)) ?></p>
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
              <?= sm_h($env['name']) ?>
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
              <?= sm_h($site['name']) ?>
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
              <?= sm_h($zone['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="sm-search-actions">
      <button class="sm-btn sm-btn-primary sm-btn-large" type="submit">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <?= sm_h(sm_t('show_requirements', $lang)) ?>
      </button>
    </div>
  </form>
</div>

<section class="sm-result-section" aria-labelledby="search-task-cards-heading">
  <div class="sm-section-header">
    <h2 class="sm-section-title" id="search-task-cards-heading">Tehtävät</h2>
    <?php if ($hasFilter): ?>
      <span class="sm-badge sm-badge-published"><?= count($taskCards ?? []) ?></span>
    <?php endif; ?>
  </div>

  <?php if (!$hasFilter): ?>
    <div class="sm-empty">
      <div class="sm-empty-icon" aria-hidden="true">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
      </div>
      <p>Valitse toimintaympäristö ja työmaa, niin tehtäväkortit latautuvat alle.</p>
    </div>
  <?php elseif (empty($taskCards)): ?>
    <div class="sm-empty">
      <p>Valituilla suodattimilla ei löytynyt tehtäviä.</p>
    </div>
  <?php else: ?>
    <div class="sm-task-card-grid">
      <?php foreach ($taskCards as $card):
        $task = $card['task'];
        $taskResult = $card['result'];
        $summary = $card['summary'];
        $ctxEnv  = $taskResult['context']['env']  ?? null;
        $ctxSite = $taskResult['context']['site'] ?? null;
        $ctxZone = $taskResult['context']['zone'] ?? null;
        $modalId = 'sm-task-modal-' . (int)$task['id'];
      ?>
        <article class="sm-task-post-card">
          <button type="button" class="sm-task-card-open" data-modal-open="<?= sm_h($modalId) ?>">
            <div class="sm-task-post-head">
              <div class="sm-task-post-icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
              </div>
              <div>
                <h3><?= sm_h((string)$task['name']) ?></h3>
                <p><?= sm_h((string)($task['description'] ?: $task['category'])) ?></p>
              </div>
            </div>
            <div class="sm-task-post-meta">
              <?php if ($ctxEnv): ?><span class="sm-context-chip"><?= sm_h((string)$ctxEnv['name']) ?></span><?php endif; ?>
              <?php if ($ctxSite): ?><span class="sm-context-chip"><?= sm_h((string)$ctxSite['name']) ?></span><?php endif; ?>
              <?php if ($ctxZone): ?><span class="sm-context-chip"><?= sm_h((string)$ctxZone['name']) ?></span><?php endif; ?>
            </div>
            <div class="sm-task-post-stats">
              <span class="sm-badge sm-badge-mandatory"><?= (int)$summary['always'] ?> pakollinen</span>
              <span class="sm-badge sm-badge-conditional"><?= (int)$summary['conditional'] ?> tilanteen mukaan</span>
              <span class="sm-badge sm-badge-published"><?= (int)$summary['other_safety'] ?> muu varuste</span>
            </div>
          </button>
        </article>

        <dialog class="sm-modal" id="<?= sm_h($modalId) ?>">
          <div class="sm-modal-card">
            <div class="sm-modal-header">
              <div>
                <h3 class="sm-modal-title"><?= sm_h((string)$task['name']) ?></h3>
                <div class="sm-task-post-meta">
                  <?php if ($ctxEnv): ?><span class="sm-context-chip"><?= sm_h((string)$ctxEnv['name']) ?></span><?php endif; ?>
                  <?php if ($ctxSite): ?><span class="sm-context-chip"><?= sm_h((string)$ctxSite['name']) ?></span><?php endif; ?>
                  <?php if ($ctxZone): ?><span class="sm-context-chip"><?= sm_h((string)$ctxZone['name']) ?></span><?php endif; ?>
                </div>
              </div>
              <button type="button" class="sm-btn sm-btn-ghost sm-btn-sm" data-modal-close>&times;</button>
            </div>

            <div class="sm-modal-content">
              <p class="sm-task-modal-desc"><?= sm_h((string)($task['description'] ?: $task['category'])) ?></p>

              <?php $sections = $taskResult['sections']; ?>
              <?php if (!empty($sections['always'])): ?>
                <section class="sm-result-section">
                  <div class="sm-section-header">
                    <h4 class="sm-section-title"><?= sm_h(sm_t('section_always', $lang)) ?></h4>
                    <span class="sm-badge sm-badge-mandatory"><?= count($sections['always']) ?></span>
                  </div>
                  <div class="sm-grid-cards">
                    <?php foreach ($sections['always'] as $item): ?><?= sm_render_ppe_card($item, $lang) ?><?php endforeach; ?>
                  </div>
                </section>
              <?php endif; ?>

              <?php if (!empty($sections['conditional'])): ?>
                <section class="sm-result-section">
                  <div class="sm-section-header">
                    <h4 class="sm-section-title"><?= sm_h(sm_t('section_conditional', $lang)) ?></h4>
                    <span class="sm-badge sm-badge-conditional"><?= count($sections['conditional']) ?></span>
                  </div>
                  <div class="sm-grid-cards">
                    <?php foreach ($sections['conditional'] as $item): ?><?= sm_render_ppe_card($item, $lang) ?><?php endforeach; ?>
                  </div>
                </section>
              <?php endif; ?>

              <?php if (!empty($sections['other_safety'])): ?>
                <section class="sm-result-section">
                  <div class="sm-section-header">
                    <h4 class="sm-section-title"><?= sm_h(sm_t('section_other', $lang)) ?></h4>
                    <span class="sm-badge sm-badge-published"><?= count($sections['other_safety']) ?></span>
                  </div>
                  <div class="sm-grid-cards">
                    <?php foreach ($sections['other_safety'] as $item): ?><?= sm_render_ppe_card($item, $lang) ?><?php endforeach; ?>
                  </div>
                </section>
              <?php endif; ?>

              <section class="sm-result-section">
                <div class="sm-section-header">
                  <h4 class="sm-section-title"><?= sm_h(sm_t('section_critical', $lang)) ?></h4>
                </div>
                <?php if (!empty($sections['information'])): ?>
                  <div class="sm-info-list">
                    <?php foreach ($sections['information'] as $info): ?>
                      <div class="sm-info-row">
                        <strong><?= sm_h((string)$info['ppe']['name']) ?></strong>
                        <?php if (!empty($info['rule']['notes'])): ?><p><?= sm_h((string)$info['rule']['notes']) ?></p><?php endif; ?>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php else: ?>
                  <div class="sm-empty sm-empty-compact"><p>Ei erillisiä huomioita.</p></div>
                <?php endif; ?>
              </section>

              <section class="sm-result-section">
                <div class="sm-section-header">
                  <h4 class="sm-section-title"><?= sm_h(sm_t('section_attachments', $lang)) ?></h4>
                </div>
                <div class="sm-empty sm-empty-compact"><p>Ei liitteitä tai karttoja tälle tehtävälle.</p></div>
              </section>
            </div>

            <div class="sm-modal-footer">
              <?php if ($isAdmin): ?>
                <a href="<?= sm_h(sm_base_url()) ?>/index.php?page=dashboard&tab=rules" class="sm-btn sm-btn-secondary sm-btn-sm">
                  <?= sm_h(sm_t('add_equipment_to_task', $lang)) ?>
                </a>
              <?php endif; ?>
              <button type="button" class="sm-btn sm-btn-primary sm-btn-sm" data-modal-close>Sulje</button>
            </div>
          </div>
        </dialog>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<script type="module" src="<?= sm_h(sm_base_url()) ?>/assets/js/modules/search.js"></script>
