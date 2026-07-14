<?php $user = sm_current_user(); $lang = $_SESSION['sm_lang'] ?? 'fi'; $page = $_GET['page'] ?? 'dashboard'; ?>
<header class="sm-nav" role="banner">
  <div class="sm-nav-inner">

    <a class="sm-brand" href="<?= sm_h(sm_base_url()) ?>/index.php?page=dashboard">
      <span class="sm-brand-icon" aria-hidden="true">🛡️</span>
      <span class="sm-brand-text"><?= sm_h(sm_t('app_title', $lang)) ?></span>
    </a>

    <?php if ($user): ?>
      <nav class="sm-top-links" id="sm-main-nav" aria-label="Päänavigaatio">
        <a href="<?= sm_h(sm_base_url()) ?>/index.php?page=dashboard"
           class="<?= $page === 'dashboard' ? 'sm-nav-active' : '' ?>"
           aria-current="<?= $page === 'dashboard' ? 'page' : 'false' ?>">
          <span aria-hidden="true">📋</span><?= sm_h(sm_t('nav_dashboard', $lang)) ?>
        </a>
        <a href="<?= sm_h(sm_base_url()) ?>/index.php?page=search"
           class="<?= $page === 'search' ? 'sm-nav-active' : '' ?>"
           aria-current="<?= $page === 'search' ? 'page' : 'false' ?>">
          <span aria-hidden="true">🔍</span><?= sm_h(sm_t('nav_search', $lang)) ?>
        </a>
      </nav>

      <div class="sm-nav-right">
        <div class="sm-lang-switcher" role="navigation" aria-label="<?= sm_h(sm_t('language', $lang)) ?>">
          <?php foreach (($smConfig['languages'] ?? ['fi']) as $code): ?>
            <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=set_language" class="sm-lang-form">
              <?= sm_csrf_field() ?>
              <input type="hidden" name="lang" value="<?= sm_h($code) ?>">
              <input type="hidden" name="page" value="<?= sm_h($page) ?>">
              <button type="submit" class="sm-lang-flag-btn <?= $code === $lang ? 'active' : '' ?>"
                      aria-label="<?= sm_h(strtoupper($code)) ?>" title="<?= sm_h(strtoupper($code)) ?>">
                <img src="<?= sm_h(sm_base_url()) ?>/assets/img/flags/<?= sm_h($code) ?>.svg"
                     alt="<?= sm_h(strtoupper($code)) ?>" class="sm-lang-flag-img" width="32" height="32">
              </button>
            </form>
          <?php endforeach; ?>
        </div>

        <span class="sm-user-chip">
          <span class="sm-user-avatar" aria-hidden="true"><?= sm_h(strtoupper(substr((string)($user['display_name'] ?? $user['email'] ?? '?'), 0, 1))) ?></span>
          <span><?= sm_h((string)($user['display_name'] ?? $user['email'] ?? '')) ?></span>
        </span>

        <a class="sm-logout" href="<?= sm_h(sm_base_url()) ?>/index.php?action=logout" aria-label="<?= sm_h(sm_t('logout', $lang)) ?>">
          <span aria-hidden="true">↩</span> <?= sm_h(sm_t('logout', $lang)) ?>
        </a>
      </div>

      <button class="sm-nav-menu-toggle" aria-expanded="false" aria-controls="sm-main-nav" aria-label="Valikko">☰</button>
    <?php endif; ?>

  </div>
</header>
