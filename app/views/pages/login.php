<section class="sm-login-card">
  <div class="sm-card">
    <div style="text-align:center;margin-bottom:1.5rem">
      <span style="font-size:2.5rem;line-height:1" aria-hidden="true">🛡️</span>
      <h1 class="sm-login-title"><?= sm_h(sm_t('login')) ?></h1>
      <p class="sm-login-subtitle">Suojainmatriisi — Hallintajärjestelmä</p>
    </div>
    <?php if (!empty($_SESSION['sm_flash_error'])): ?>
      <div class="sm-alert sm-alert-danger" role="alert"><?= sm_h((string)$_SESSION['sm_flash_error']) ?></div>
      <?php unset($_SESSION['sm_flash_error']); ?>
    <?php endif; ?>
    <form method="post" action="<?= sm_h(sm_base_url()) ?>/index.php?action=login" class="sm-form-grid">
      <?= sm_csrf_field() ?>
      <label>Sähköposti <input type="email" name="email" required autocomplete="username" placeholder="you@example.com"></label>
      <label>Salasana <input type="password" name="password" required autocomplete="current-password" placeholder="••••••••"></label>
      <button type="submit" class="sm-btn sm-btn-primary" style="width:100%;justify-content:center;margin-top:.25rem"><?= sm_h(sm_t('login')) ?></button>
    </form>
  </div>
</section>
