<section class="sm-page-header">
  <h1><?= sm_h(sm_t('nav_dashboard')) ?></h1>
</section>

<section class="sm-grid-2">
  <article class="sm-card">
    <h2>Työmaat</h2>
    <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=save_site" class="sm-form-grid">
      <?= sm_csrf_field() ?>
      <label>Nimi <input name="name" required></label>
      <label>Koodi <input name="code" required></label>
      <button class="sm-btn-primary" type="submit">Tallenna</button>
    </form>
    <ul><?php foreach ($data['sites'] as $site): ?><li><?= sm_h($site['name']) ?> (<?= sm_h($site['code']) ?>)</li><?php endforeach; ?></ul>
  </article>

  <article class="sm-card">
    <h2>Työtehtävät</h2>
    <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=save_task" class="sm-form-grid">
      <?= sm_csrf_field() ?>
      <label>Nimi <input name="name" required></label>
      <label>Kategoria <input name="category" required></label>
      <button class="sm-btn-primary" type="submit">Tallenna</button>
    </form>
    <ul><?php foreach ($data['tasks'] as $task): ?><li><?= sm_h($task['name']) ?> / <?= sm_h($task['category']) ?></li><?php endforeach; ?></ul>
  </article>

  <article class="sm-card">
    <h2>Suojainkirjasto</h2>
    <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=save_ppe" class="sm-form-grid">
      <?= sm_csrf_field() ?>
      <label>Koodi <input name="code" required></label>
      <label>Nimi <input name="name" required></label>
      <label>Kategoria <input name="category" required></label>
      <label>Ikoni <input name="icon" value="helmet.svg" required></label>
      <button class="sm-btn-primary" type="submit">Tallenna</button>
    </form>
    <ul><?php foreach ($data['ppeItems'] as $item): ?><li><?= sm_h($item['name']) ?> (<?= sm_h($item['category']) ?>)</li><?php endforeach; ?></ul>
  </article>

  <article class="sm-card">
    <h2>Vaatimussääntö</h2>
    <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=save_rule" class="sm-form-grid">
      <?= sm_csrf_field() ?>
      <label>Soveltuvuus
        <select name="scope_type">
          <option value="global">Yleinen</option>
          <option value="site">Työmaakohtainen</option>
          <option value="task">Työtehtäväkohtainen</option>
          <option value="local">Paikallinen (työmaa+tehtävä)</option>
        </select>
      </label>
      <label>Työmaa
        <select name="site_id"><option value="0">-</option><?php foreach ($data['sites'] as $site): ?><option value="<?= (int)$site['id'] ?>"><?= sm_h($site['name']) ?></option><?php endforeach; ?></select>
      </label>
      <label>Työtehtävä
        <select name="task_id"><option value="0">-</option><?php foreach ($data['tasks'] as $task): ?><option value="<?= (int)$task['id'] ?>"><?= sm_h($task['name']) ?></option><?php endforeach; ?></select>
      </label>
      <label>Suojain
        <select name="ppe_item_id"><?php foreach ($data['ppeItems'] as $item): ?><option value="<?= (int)$item['id'] ?>"><?= sm_h($item['name']) ?></option><?php endforeach; ?></select>
      </label>
      <label>Taso
        <select name="requirement_level">
          <option value="required"><?= sm_h(sm_t('required')) ?></option>
          <option value="recommended"><?= sm_h(sm_t('recommended')) ?></option>
          <option value="forbidden"><?= sm_h(sm_t('forbidden')) ?></option>
        </select>
      </label>
      <label>Tila
        <select name="status">
          <option value="draft">luonnos</option>
          <option value="review">tarkastettavana</option>
          <option value="approved">hyväksytty</option>
          <option value="published">julkaistu</option>
          <option value="archived">arkistoitu</option>
        </select>
      </label>
      <label>Huomio <input name="notes"></label>
      <button class="sm-btn-primary" type="submit">Tallenna sääntö</button>
    </form>

    <table class="sm-table">
      <thead><tr><th>Scope</th><th>PPE</th><th>Taso</th><th>Tila</th><th>Versio</th><th>Työnkulku</th></tr></thead>
      <tbody>
      <?php foreach ($data['rules'] as $rule): ?>
      <tr>
        <td><?= sm_h($rule['scope_type']) ?> <?= sm_h((string)$rule['site_name']) ?> <?= sm_h((string)$rule['task_name']) ?></td>
        <td><?= sm_h($rule['ppe_name']) ?></td>
        <td><?= sm_h($rule['requirement_level']) ?></td>
        <td><?= sm_h($rule['status']) ?></td>
        <td><?= (int)$rule['version_no'] ?></td>
        <td>
          <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=change_rule_status" class="sm-inline-form">
            <?= sm_csrf_field() ?>
            <input type="hidden" name="rule_id" value="<?= (int)$rule['id'] ?>">
            <select name="status">
              <option value="review">review</option><option value="approved">approved</option><option value="published">published</option><option value="archived">archived</option><option value="draft">draft</option>
            </select>
            <button class="sm-btn-secondary" type="submit">Siirrä</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </article>
</section>

<section class="sm-card">
  <h2>Audit-loki</h2>
  <ul class="sm-audit-list">
    <?php foreach ($data['audit'] as $entry): ?>
      <li><strong><?= sm_h((string)$entry['event_type']) ?></strong> — <?= sm_h((string)$entry['display_name']) ?> (<?= sm_h((string)$entry['created_at']) ?>)</li>
    <?php endforeach; ?>
  </ul>
</section>
