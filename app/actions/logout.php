<?php
declare(strict_types=1);

sm_auth_logout();
sm_redirect('/index.php?page=login');
