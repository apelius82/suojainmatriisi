<section class="sm-card">
  <h1><?= sm_h(sm_t('search_worker')) ?></h1>
  <form class="sm-form-grid" method="get" action="<?= sm_h(sm_base_url()) ?>/index.php">
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
      <input id="worker-search" name="q" value="<?= sm_h((string)($_GET['q'] ?? '')) ?>" autocomplete="off" list="worker-options">
      <datalist id="worker-options"></datalist>
    </label>
    <button class="sm-btn-primary" type="submit">Hae</button>
  </form>
</section>

<section class="sm-grid-cards">
  <?php foreach ($result['cards'] as $card): ?>
    <article class="sm-ppe-card">
      <img src="<?= sm_h(sm_base_url()) ?>/assets/img/ppe/<?= sm_h((string)$card['ppe']['icon']) ?>" alt="" width="48" height="48">
      <h3><?= sm_h((string)$card['ppe']['name']) ?></h3>
      <p class="sm-level sm-level-<?= sm_h((string)$card['rule']['requirement_level']) ?>"><?= sm_h(sm_t((string)$card['rule']['requirement_level'])) ?></p>
      <p><?= sm_h((string)$card['rule']['scope_type']) ?></p>
    </article>
  <?php endforeach; ?>
</section>

<?php if (!empty($result['conflicts'])): ?>
<section class="sm-card sm-warning">
  <h2>Ristiriidat</h2>
  <ul><?php foreach ($result['conflicts'] as $conflict): ?><li>PPE #<?= (int)$conflict['ppe_item_id'] ?>: <?= sm_h((string)$conflict['discarded']['scope_type']) ?> ohitettu</li><?php endforeach; ?></ul>
</section>
<?php endif; ?>

<section class="sm-print-actions"><button onclick="window.print()" class="sm-btn-secondary"><?= sm_h(sm_t('print')) ?></button></section>
<script type="module" src="<?= sm_h(sm_base_url()) ?>/assets/js/modules/search.js"></script>
