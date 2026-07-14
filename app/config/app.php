<?php
declare(strict_types=1);

return [
    'app_name' => getenv('APP_NAME') ?: 'Suojainmatriisi',
    'env' => getenv('APP_ENV') ?: 'production',
    'base_url' => rtrim((string)(getenv('APP_BASE_URL') ?: ''), '/'),
    'default_language' => getenv('DEFAULT_LANGUAGE') ?: 'fi',
    'languages' => ['fi', 'sv', 'en', 'it', 'el'],
    'session' => [
        'name' => getenv('SESSION_NAME') ?: 'sm_session',
        'cookie_name' => getenv('SESSION_COOKIE') ?: 'sm_identity',
        'lifetime' => (int)(getenv('SESSION_LIFETIME') ?: 28800),
    ],
    'db' => [
        'dsn' => getenv('DB_DSN') ?: '',
        'user' => getenv('DB_USER') ?: '',
        'pass' => getenv('DB_PASS') ?: '',
    ],
    'log_file' => __DIR__ . '/../../storage/logs/suojainmatriisi.log',
];
