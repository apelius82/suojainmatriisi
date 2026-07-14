<?php $lang = $_SESSION['sm_lang'] ?? 'fi'; ?>
<footer class="sm-footer-nav" role="navigation" aria-label="Mobiilinavigaatio">
  <a href="<?= sm_h(sm_base_url()) ?>/index.php?page=dashboard"><?= sm_h(sm_t('nav_dashboard', $lang)) ?></a>
  <a href="<?= sm_h(sm_base_url()) ?>/index.php?page=search"><?= sm_h(sm_t('nav_search', $lang)) ?></a>
</footer>
