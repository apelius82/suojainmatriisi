<div class="sm-page-header">
  <div class="sm-page-header-left">
    <h1><?= sm_h(sm_t('search_worker')) ?></h1>
    <p class="sm-page-subtitle">Valitse työmaa ja tehtävä nähdäksesi voimassa olevat suojainsäännöt</p>
  </div>
</div>

<div class="sm-card">
  <form class="sm-form-row" method="get" action="<?= sm_h(sm_base_url()) ?>/index.php" role="search">
    <input type="hidden" name="page" value="search">
    <label>Työmaa
      <select name="site_id">
        <?php foreach ($sites as $site): ?>
          <option value="<?= (int)$site['id'] ?>" <?= (int)($_GET['site_id'] ?? 0) === (int)$site['id'] ? 'selected' : '' ?>>
            <?= sm_h($site['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Työtehtävä
      <select name="task_id">
        <?php foreach ($tasks as $task): ?>
          <option value="<?= (int)$task['id'] ?>" <?= (int)($_GET['task_id'] ?? 0) === (int)$task['id'] ? 'selected' : '' ?>>
            <?= sm_h($task['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Työntekijä
      <input id="worker-search" name="q" value="<?= sm_h((string)($_GET['q'] ?? '')) ?>" autocomplete="off" list="worker-options" placeholder="Hae nimellä…">
      <datalist id="worker-options"></datalist>
    </label>
    <label style="align-self:end">
      <button class="sm-btn sm-btn-primary" type="submit">
        <span aria-hidden="true">🔍</span> Hae
      </button>
    </label>
  </form>
</div>

<?php if (!empty($result['cards'])): ?>
<div class="sm-section-header" style="margin-top:1.5rem">
  <h2 class="sm-section-title">Suojainvaatimukset <span class="sm-badge sm-badge-published"><?= count($result['cards']) ?></span></h2>
</div>
<div class="sm-grid-cards">
  <?php foreach ($result['cards'] as $card): ?>
    <article class="sm-ppe-card">
      <img src="<?= sm_h(sm_base_url()) ?>/assets/img/ppe/<?= sm_h((string)$card['ppe']['icon']) ?>"
           alt="" width="48" height="48" loading="lazy">
      <h3><?= sm_h((string)$card['ppe']['name']) ?></h3>
      <span class="sm-badge sm-badge-<?= sm_h((string)$card['rule']['requirement_level']) ?>">
        <?= sm_h(sm_t((string)$card['rule']['requirement_level'])) ?>
      </span>
      <p class="sm-ppe-scope"><?= sm_h((string)$card['rule']['scope_type']) ?></p>
    </article>
  <?php endforeach; ?>
</div>
<?php elseif (isset($_GET['site_id'])): ?>
<div class="sm-empty" style="margin-top:1.5rem">
  <div class="sm-empty-icon" aria-hidden="true">🦺</div>
  <p>Ei suojainsääntöjä tälle valinnalle.</p>
</div>
<?php endif; ?>

<?php if (!empty($result['conflicts'])): ?>
<section class="sm-card sm-warning" style="margin-top:1.25rem" aria-label="Ristiriidat">
  <div class="sm-section-header">
    <h2 class="sm-section-title">⚠️ Ristiriidat</h2>
    <span class="sm-badge sm-badge-review"><?= count($result['conflicts']) ?> ohitettu</span>
  </div>
  <div class="sm-list-block">
    <?php foreach ($result['conflicts'] as $conflict): ?>
      <div class="sm-list-row">
        <span class="sm-list-row-icon" aria-hidden="true">⚠️</span>
        <div class="sm-list-row-main">
          <div class="sm-list-row-name">Suojain #<?= (int)$conflict['ppe_item_id'] ?></div>
          <div class="sm-list-row-meta">Ohitettu sääntö: <?= sm_h((string)$conflict['discarded']['scope_type']) ?></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<div class="sm-print-actions">
  <button onclick="window.print()" class="sm-btn sm-btn-secondary">
    <span aria-hidden="true">🖨️</span> <?= sm_h(sm_t('print')) ?>
  </button>
</div>

<script type="module" src="<?= sm_h(sm_base_url()) ?>/assets/js/modules/search.js"></script>
