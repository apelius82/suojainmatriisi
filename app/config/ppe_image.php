<?php
declare(strict_types=1);
/**
 * Jaettu PPE-kuvaformaattien konfiguraatio.
 * Käytetään sekä latauksessa (upload_ppe_image.php) että palvelussa (ppe_image.php).
 */
return [
    // MIME type → tiedostopääte -muunnos (käytetään latauksessa)
    'mime_to_ext' => [
        'image/svg+xml' => 'svg',
        'image/jpeg'    => 'jpg',
        'image/png'     => 'png',
        'image/webp'    => 'webp',
    ],
    // Tiedostopääte → MIME type -muunnos (käytetään palvelussa)
    'ext_to_mime' => [
        'svg'  => 'image/svg+xml',
        'jpg'  => 'image/jpeg',
        'png'  => 'image/png',
        'webp' => 'image/webp',
    ],
    // Max-koko tavuina
    'max_bytes' => 2 * 1024 * 1024,
];
