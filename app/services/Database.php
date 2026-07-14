<?php
declare(strict_types=1);

final class Database
{
    private static ?PDO $pdo = null;

    public static function connect(array $dbConfig): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        if (empty($dbConfig['dsn'])) {
            throw new RuntimeException('DB_DSN puuttuu ympäristömuuttujista.');
        }

        self::$pdo = new PDO(
            (string)$dbConfig['dsn'],
            (string)($dbConfig['user'] ?? ''),
            (string)($dbConfig['pass'] ?? ''),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );

        return self::$pdo;
    }
}
