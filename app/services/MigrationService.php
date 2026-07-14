<?php
declare(strict_types=1);

final class MigrationService
{
    public static function migrate(PDO $pdo, string $migrationDir): void
    {
        $pdo->exec('CREATE TABLE IF NOT EXISTS ppe_migrations (id INT AUTO_INCREMENT PRIMARY KEY, filename VARCHAR(255) UNIQUE NOT NULL, executed_at DATETIME NOT NULL)');
        $done = $pdo->query('SELECT filename FROM ppe_migrations')->fetchAll(PDO::FETCH_COLUMN) ?: [];

        $files = glob($migrationDir . '/*.sql') ?: [];
        sort($files);

        foreach ($files as $file) {
            $filename = basename($file);
            if (in_array($filename, $done, true)) {
                continue;
            }
            $sql = file_get_contents($file) ?: '';
            if (trim($sql) === '') {
                continue;
            }
            $pdo->exec($sql);
            $stmt = $pdo->prepare('INSERT INTO ppe_migrations (filename, executed_at) VALUES (?, NOW())');
            $stmt->execute([$filename]);
        }
    }

    public static function seed(PDO $pdo, string $seedDir): void
    {
        $files = glob($seedDir . '/*.sql') ?: [];
        sort($files);
        foreach ($files as $file) {
            $sql = file_get_contents($file) ?: '';
            if (trim($sql) !== '') {
                $pdo->exec($sql);
            }
        }
    }
}
