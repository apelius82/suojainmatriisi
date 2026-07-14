<?php
$activeTab = (string)($_GET['tab'] ?? 'sites');
$validTabs = ['sites','tasks','ppe','rules','audit'];
if (!in_array($activeTab, $validTabs, true)) $activeTab = 'sites';
?>

<div class="sm-page-header">
  <div class="sm-page-header-left">
    <h1><?= sm_h(sm_t('nav_dashboard')) ?></h1>
    <p class="sm-page-subtitle">Hallinnoi työmaat, tehtävät, suojainkirjasto ja vaatimussäännöt</p>
  </div>
</div>

<?php
$tabCounts = [
  'sites' => count($data['sites']),
  'tasks' => count($data['tasks']),
  'ppe'   => count($data['ppeItems']),
  'rules' => count($data['rules']),
  'audit' => count($data['audit']),
];
$tabs = [
  'sites' => ['icon'=>'🏗️','label'=>'Työmaat'],
  'tasks' => ['icon'=>'⚙️','label'=>'Työtehtävät'],
  'ppe'   => ['icon'=>'🦺','label'=>'Suojainkirjasto'],
  'rules' => ['icon'=>'📜','label'=>'Vaatimussäännöt'],
  'audit' => ['icon'=>'🕒','label'=>'Audit-loki'],
];
?>

<nav class="sm-admin-tabs" role="tablist" aria-label="Hallintaosiot">
  <?php foreach ($tabs as $key => $tab): ?>
    <a href="<?= sm_h(sm_base_url()) ?>/index.php?page=dashboard&tab=<?= sm_h($key) ?>"
       role="tab"
       class="sm-admin-tab <?= $activeTab === $key ? 'active' : '' ?>"
       aria-selected="<?= $activeTab === $key ? 'true' : 'false' ?>">
      <span class="sm-tab-icon" aria-hidden="true"><?= $tab['icon'] ?></span>
      <?= sm_h($tab['label']) ?>
      <span class="sm-tab-badge"><?= (int)$tabCounts[$key] ?></span>
    </a>
  <?php endforeach; ?>
</nav>

<?php /* ============================================================
   TAB: TYÖMAAT
   ============================================================ */ if ($activeTab === 'sites'): ?>
<section aria-labelledby="tab-sites-heading">
  <div class="sm-section-header">
    <h2 class="sm-section-title" id="tab-sites-heading">🏗️ Työmaat</h2>
  </div>

  <div class="sm-card">
    <?php if (empty($data['sites'])): ?>
      <div class="sm-empty"><div class="sm-empty-icon" aria-hidden="true">🏗️</div><p>Ei työmaarekisteröintejä. Lisää ensimmäinen työmaa allaolevalla lomakkeella.</p></div>
    <?php else: ?>
      <div class="sm-table-wrap">
        <table class="sm-table" aria-label="Työmaat">
          <thead>
            <tr>
              <th scope="col">Nimi</th>
              <th scope="col">Koodi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($data['sites'] as $site): ?>
              <tr>
                <td><strong><?= sm_h($site['name']) ?></strong></td>
                <td><code><?= sm_h($site['code']) ?></code></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

    <div class="sm-add-form-block" role="region" aria-label="Lisää työmaa">
      <h3>+ Lisää uusi työmaa</h3>
      <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=save_site" class="sm-form-row">
        <?= sm_csrf_field() ?>
        <label>Nimi <input name="name" required autocomplete="off" placeholder="Työmaan nimi"></label>
        <label>Koodi <input name="code" required autocomplete="off" placeholder="Esim. TM-001"></label>
        <label style="align-self:end"><button class="sm-btn sm-btn-primary" type="submit">Tallenna</button></label>
      </form>
    </div>
  </div>
</section>

<?php /* ============================================================
   TAB: TYÖTEHTÄVÄT
   ============================================================ */ elseif ($activeTab === 'tasks'): ?>
<section aria-labelledby="tab-tasks-heading">
  <div class="sm-section-header">
    <h2 class="sm-section-title" id="tab-tasks-heading">⚙️ Työtehtävät</h2>
  </div>

  <div class="sm-card">
    <?php if (empty($data['tasks'])): ?>
      <div class="sm-empty"><div class="sm-empty-icon" aria-hidden="true">⚙️</div><p>Ei tehtävärekisteröintejä. Lisää ensimmäinen tehtävä allaolevalla lomakkeella.</p></div>
    <?php else: ?>
      <div class="sm-list-block">
        <?php foreach ($data['tasks'] as $task): ?>
          <div class="sm-list-row">
            <span class="sm-list-row-icon" aria-hidden="true">⚙️</span>
            <div class="sm-list-row-main">
              <div class="sm-list-row-name"><?= sm_h($task['name']) ?></div>
              <div class="sm-list-row-meta"><?= sm_h($task['category']) ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div class="sm-add-form-block" role="region" aria-label="Lisää tehtävä">
      <h3>+ Lisää uusi tehtävä</h3>
      <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=save_task" class="sm-form-row">
        <?= sm_csrf_field() ?>
        <label>Nimi <input name="name" required autocomplete="off" placeholder="Tehtävän nimi"></label>
        <label>Kategoria <input name="category" required autocomplete="off" placeholder="Esim. Korkeustyöt"></label>
        <label style="align-self:end"><button class="sm-btn sm-btn-primary" type="submit">Tallenna</button></label>
      </form>
    </div>
  </div>
</section>

<?php /* ============================================================
   TAB: SUOJAINKIRJASTO
   ============================================================ */ elseif ($activeTab === 'ppe'): ?>
<section aria-labelledby="tab-ppe-heading">
  <div class="sm-section-header">
    <h2 class="sm-section-title" id="tab-ppe-heading">🦺 Suojainkirjasto</h2>
  </div>

  <div class="sm-card">
    <?php if (empty($data['ppeItems'])): ?>
      <div class="sm-empty"><div class="sm-empty-icon" aria-hidden="true">🦺</div><p>Suojainkirjasto on tyhjä. Lisää ensimmäinen suojain allaolevalla lomakkeella.</p></div>
    <?php else: ?>
      <div class="sm-table-wrap">
        <table class="sm-table" aria-label="Suojainkirjasto">
          <thead>
            <tr>
              <th scope="col" style="width:44px"></th>
              <th scope="col">Nimi</th>
              <th scope="col">Koodi</th>
              <th scope="col">Kategoria</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($data['ppeItems'] as $item): ?>
              <tr>
                <td><img src="<?= sm_h(sm_base_url()) ?>/assets/img/ppe/<?= sm_h((string)$item['icon']) ?>" alt="" width="28" height="28" style="display:block;object-fit:contain;"></td>
                <td><strong><?= sm_h($item['name']) ?></strong></td>
                <td><code><?= sm_h($item['code']) ?></code></td>
                <td><?= sm_h($item['category']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

    <div class="sm-add-form-block" role="region" aria-label="Lisää suojain">
      <h3>+ Lisää uusi suojain</h3>
      <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=save_ppe" class="sm-rule-form-grid">
        <?= sm_csrf_field() ?>
        <label>Koodi <input name="code" required autocomplete="off" placeholder="Esim. PPE-001"></label>
        <label>Nimi <input name="name" required autocomplete="off" placeholder="Esim. Kypärä"></label>
        <label>Kategoria <input name="category" required autocomplete="off" placeholder="Esim. Pään suojaus"></label>
        <label>
          Ikoni
          <input name="icon" value="helmet.svg" required autocomplete="off">
          <span class="sm-icon-hint">Tiedostonimi kuten <code>helmet.svg</code>, <code>gloves.svg</code> jne.</span>
        </label>
        <div class="sm-form-full" style="padding-top:.25rem">
          <button class="sm-btn sm-btn-primary" type="submit">Tallenna suojain</button>
        </div>
      </form>
    </div>
  </div>
</section>

<?php /* ============================================================
   TAB: VAATIMUSSÄÄNNÖT
   ============================================================ */ elseif ($activeTab === 'rules'): ?>
<section aria-labelledby="tab-rules-heading">
  <div class="sm-section-header">
    <h2 class="sm-section-title" id="tab-rules-heading">📜 Vaatimussäännöt</h2>
  </div>

  <?php if (!empty($data['rules'])): ?>
  <div class="sm-card" style="padding: 0; overflow:hidden;">
    <div class="sm-table-wrap">
      <table class="sm-table" aria-label="Vaatimussäännöt">
        <thead>
          <tr>
            <th scope="col">Kattavuus</th>
            <th scope="col">Suojain</th>
            <th scope="col">Taso</th>
            <th scope="col">Tila</th>
            <th scope="col">Ver.</th>
            <th scope="col">Muuta tilaa</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($data['rules'] as $rule):
            $scopeKey = $rule['scope_type'];
            $scopeLabel = match($scopeKey) { 'global'=>'Yleinen','site'=>'Työmaa','task'=>'Tehtävä','local'=>'Paikallinen', default=>$scopeKey };
            $levelKey = $rule['requirement_level'];
            $statusKey = $rule['status'];
          ?>
          <tr>
            <td>
              <span class="sm-badge sm-badge-<?= sm_h($scopeKey) ?>"><?= sm_h($scopeLabel) ?></span>
              <?php if ($rule['site_name']): ?><small style="display:block;color:var(--sm-muted);font-size:.78rem;margin-top:.15rem"><?= sm_h((string)$rule['site_name']) ?></small><?php endif; ?>
              <?php if ($rule['task_name']): ?><small style="display:block;color:var(--sm-muted);font-size:.78rem"><?= sm_h((string)$rule['task_name']) ?></small><?php endif; ?>
            </td>
            <td><strong><?= sm_h($rule['ppe_name']) ?></strong></td>
            <td><span class="sm-badge sm-badge-<?= sm_h($levelKey) ?>"><?= sm_h(sm_t($levelKey)) ?></span></td>
            <td><span class="sm-badge sm-badge-<?= sm_h($statusKey) ?>"><?= sm_h($statusKey) ?></span></td>
            <td style="color:var(--sm-muted);font-size:.85rem"><?= (int)$rule['version_no'] ?></td>
            <td>
              <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=change_rule_status" class="sm-rule-status-form">
                <?= sm_csrf_field() ?>
                <input type="hidden" name="rule_id" value="<?= (int)$rule['id'] ?>">
                <select name="status" aria-label="Uusi tila säännölle <?= (int)$rule['id'] ?>">
                  <?php foreach (['draft'=>'Luonnos','review'=>'Tarkastettavana','approved'=>'Hyväksytty','published'=>'Julkaistu','archived'=>'Arkistoitu'] as $v=>$l): ?>
                    <option value="<?= sm_h($v) ?>" <?= $statusKey === $v ? 'selected' : '' ?>><?= sm_h($l) ?></option>
                  <?php endforeach; ?>
                </select>
                <button class="sm-btn sm-btn-secondary sm-btn-sm" type="submit">Siirrä</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php else: ?>
    <div class="sm-empty"><div class="sm-empty-icon" aria-hidden="true">📜</div><p>Vaatimussääntöjä ei ole vielä lisätty.</p></div>
  <?php endif; ?>

  <div class="sm-card sm-add-form-block" style="border-style:dashed;background:var(--sm-surface-2)" role="region" aria-label="Lisää vaatimussääntö">
    <h3>+ Lisää uusi vaatimussääntö</h3>

    <div class="sm-status-strip" aria-label="Työnkulun tilat">
      <?php foreach (['draft'=>['Luonnos','sm-badge-draft'],'review'=>['Tarkastettavana','sm-badge-review'],'approved'=>['Hyväksytty','sm-badge-approved'],'published'=>['Julkaistu','sm-badge-published'],'archived'=>['Arkistoitu','sm-badge-archived']] as $v=>[$l,$cls]): ?>
        <span class="sm-status-step sm-badge <?= $cls ?>"><?= sm_h($l) ?></span>
      <?php endforeach; ?>
      <span style="font-size:.78rem;color:var(--sm-muted);margin-left:.25rem">— työnkulun vaihe</span>
    </div>

    <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=save_rule" class="sm-rule-form-grid">
      <?= sm_csrf_field() ?>
      <label>Soveltuvuus
        <select name="scope_type">
          <option value="global">Yleinen</option>
          <option value="site">Työmaakohtainen</option>
          <option value="task">Työtehtäväkohtainen</option>
          <option value="local">Paikallinen (työmaa + tehtävä)</option>
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
      <label>Työtehtävä
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
          <option value="required"><?= sm_h(sm_t('required')) ?></option>
          <option value="recommended"><?= sm_h(sm_t('recommended')) ?></option>
          <option value="forbidden"><?= sm_h(sm_t('forbidden')) ?></option>
        </select>
      </label>
      <label>Tila
        <select name="status">
          <option value="draft">Luonnos</option>
          <option value="review">Tarkastettavana</option>
          <option value="approved">Hyväksytty</option>
          <option value="published">Julkaistu</option>
          <option value="archived">Arkistoitu</option>
        </select>
      </label>
      <label class="sm-form-full">Huomio / Lisätiedot
        <input name="notes" autocomplete="off" placeholder="Vapaaehtoinen huomio tai perustelu">
      </label>
      <div class="sm-form-full">
        <button class="sm-btn sm-btn-primary" type="submit">Tallenna sääntö</button>
      </div>
    </form>
  </div>
</section>

<?php /* ============================================================
   TAB: AUDIT-LOKI
   ============================================================ */ elseif ($activeTab === 'audit'): ?>
<section aria-labelledby="tab-audit-heading">
  <div class="sm-section-header">
    <h2 class="sm-section-title" id="tab-audit-heading">🕒 Audit-loki</h2>
  </div>

  <div class="sm-card">
    <?php if (empty($data['audit'])): ?>
      <div class="sm-empty"><div class="sm-empty-icon" aria-hidden="true">🕒</div><p>Ei kirjauksia vielä.</p></div>
    <?php else: ?>
      <ul class="sm-audit-list" aria-label="Viimeisimmät tapahtumat">
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
