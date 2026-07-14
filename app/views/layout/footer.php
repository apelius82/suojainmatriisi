<?php $lang = $_SESSION['sm_lang'] ?? 'fi'; $page = $_GET['page'] ?? 'dashboard'; ?>
<footer class="sm-footer-nav" role="navigation" aria-label="Mobiilinavigaatio">
  <a href="<?= sm_h(sm_base_url()) ?>/index.php?page=dashboard" class="<?= $page === 'dashboard' ? 'sm-nav-active' : '' ?>" aria-current="<?= $page === 'dashboard' ? 'page' : 'false' ?>">
    <span class="sm-footer-nav-icon" aria-hidden="true">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
    </span>
    <span><?= sm_h(sm_t('nav_dashboard', $lang)) ?></span>
  </a>
  <a href="<?= sm_h(sm_base_url()) ?>/index.php?page=search" class="<?= $page === 'search' ? 'sm-nav-active' : '' ?>" aria-current="<?= $page === 'search' ? 'page' : 'false' ?>">
    <span class="sm-footer-nav-icon" aria-hidden="true">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
    </span>
    <span><?= sm_h(sm_t('nav_search', $lang)) ?></span>
  </a>
</footer>
