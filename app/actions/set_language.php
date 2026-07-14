<?php
declare(strict_types=1);

sm_csrf_require();
$lang = (string)($_POST['lang'] ?? 'fi');
$allowed = $smConfig['languages'] ?? ['fi'];
$_SESSION['sm_lang'] = in_array($lang, $allowed, true) ? $lang : 'fi';
sm_redirect('/index.php?page=' . urlencode((string)($_POST['page'] ?? 'dashboard')));
