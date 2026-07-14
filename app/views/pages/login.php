<section class="sm-card sm-login-card">
  <h1><?= sm_h(sm_t('login')) ?></h1>
  <?php if (!empty($_SESSION['sm_flash_error'])): ?>
    <p class="sm-error"><?= sm_h((string)$_SESSION['sm_flash_error']) ?></p>
    <?php unset($_SESSION['sm_flash_error']); ?>
  <?php endif; ?>
  <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=login" class="sm-form-grid">
    <?= sm_csrf_field() ?>
    <label>Sähköposti <input type="email" name="email" required autocomplete="username"></label>
    <label>Salasana <input type="password" name="password" required autocomplete="current-password"></label>
    <button type="submit" class="sm-btn-primary"><?= sm_h(sm_t('login')) ?></button>
  </form>
</section>
