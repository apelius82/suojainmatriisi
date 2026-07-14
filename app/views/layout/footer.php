<?php $lang = $_SESSION['sm_lang'] ?? 'fi'; $page = $_GET['page'] ?? 'dashboard'; ?>
<footer class="sm-footer-nav" role="navigation" aria-label="Mobiilinavigaatio">
  <a href="<?= sm_h(sm_base_url()) ?>/index.php?page=dashboard" class="<?= $page === 'dashboard' ? 'sm-nav-active' : '' ?>" aria-current="<?= $page === 'dashboard' ? 'page' : 'false' ?>">
    <span class="sm-footer-nav-icon" aria-hidden="true">📋</span>
    <span><?= sm_h(sm_t('nav_dashboard', $lang)) ?></span>
  </a>
  <a href="<?= sm_h(sm_base_url()) ?>/index.php?page=search" class="<?= $page === 'search' ? 'sm-nav-active' : '' ?>" aria-current="<?= $page === 'search' ? 'page' : 'false' ?>">
    <span class="sm-footer-nav-icon" aria-hidden="true">🔍</span>
    <span><?= sm_h(sm_t('nav_search', $lang)) ?></span>
  </a>
</footer>
