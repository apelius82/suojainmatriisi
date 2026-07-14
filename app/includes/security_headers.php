<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli' && !headers_sent()) {
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self'; connect-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'", false);
    header('X-Frame-Options: DENY', false);
    header('X-Content-Type-Options: nosniff', false);
    header('Referrer-Policy: strict-origin-when-cross-origin', false);
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()', false);
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0', false);
    header('Pragma: no-cache', false);
    header('Expires: 0', false);
}
