<?php $user = sm_current_user(); $lang = $_SESSION['sm_lang'] ?? 'fi'; ?>
<header class="sm-nav" role="banner">
  <div class="sm-nav-inner">
    <a class="sm-brand" href="<?= sm_h(sm_base_url()) ?>/index.php?page=dashboard">🛡️ <?= sm_h(sm_t('app_title', $lang)) ?></a>
    <?php if ($user): ?>
      <nav class="sm-top-links" aria-label="Päänavigaatio">
        <a href="<?= sm_h(sm_base_url()) ?>/index.php?page=dashboard"><?= sm_h(sm_t('nav_dashboard', $lang)) ?></a>
        <a href="<?= sm_h(sm_base_url()) ?>/index.php?page=search"><?= sm_h(sm_t('nav_search', $lang)) ?></a>
      </nav>
      <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=set_language" class="sm-lang-form">
        <?= sm_csrf_field() ?>
        <input type="hidden" name="page" value="<?= sm_h((string)($_GET['page'] ?? 'dashboard')) ?>">
        <select name="lang" onchange="this.form.submit()" aria-label="<?= sm_h(sm_t('language', $lang)) ?>">
          <?php foreach (($smConfig['languages'] ?? ['fi']) as $code): ?>
            <option value="<?= sm_h($code) ?>" <?= $code === $lang ? 'selected' : '' ?>><?= strtoupper(sm_h($code)) ?></option>
          <?php endforeach; ?>
        </select>
      </form>
      <a class="sm-logout" href="<?= sm_h(sm_base_url()) ?>/index.php?action=logout"><?= sm_h(sm_t('logout', $lang)) ?></a>
    <?php endif; ?>
  </div>
</header>
