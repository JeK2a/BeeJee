<?php

declare(strict_types=1);

namespace App;

use PDO;
use PDOException;

/**
 * Applies versioned PHP migrations from database/migrations/.
 *
 * Each file must return an array with keys:
 * - "sqlite": list of SQL strings (may be empty)
 * - "mysql": list of SQL strings (may be empty)
 * - "after" (optional): callable(PDO $pdo, string $driver): void
 */
final class MigrationRunner
{
    private const MIGRATIONS_DIR = 'database/migrations';

    public function __construct(
        private readonly PDO $pdo,
        private readonly string $driver,
    ) {
    }

    public function runPending(): int
    {
        $this->ensureMigrationsTable();

        $dir = dirname(__DIR__, 2) . '/' . self::MIGRATIONS_DIR;
        if (!is_dir($dir)) {
            return 0;
        }

        $files = glob($dir . '/*.php') ?: [];
        sort($files, SORT_STRING);

        $applied = 0;
        foreach ($files as $file) {
            $version = basename($file, '.php');
            if ($this->isApplied($version)) {
                continue;
            }

            /** @var array{sqlite?: list<string>, mysql?: list<string>, after?: callable(PDO, string): void} $def */
            $def = require $file;
            if (!is_array($def)) {
                throw new \RuntimeException('Migration must return an array: ' . $file);
            }

            $statements = $def[$this->driver] ?? null;
            if (!is_array($statements)) {
                $statements = [];
            }

            try {
                foreach ($statements as $sql) {
                    if (!is_string($sql) || $sql === '') {
                        continue;
                    }
                    $this->pdo->exec($sql);
                }
                if (isset($def['after']) && is_callable($def['after'])) {
                    $def['after']($this->pdo, $this->driver);
                }
                $this->recordApplied($version);
                ++$applied;
            } catch (PDOException $e) {
                throw new \RuntimeException('Migration failed: ' . $version . ' — ' . $e->getMessage(), 0, $e);
            }
        }

        return $applied;
    }

    private function ensureMigrationsTable(): void
    {
        if ($this->driver === 'mysql') {
            $this->pdo->exec(
                'CREATE TABLE IF NOT EXISTS `schema_migrations` (
                    `version` VARCHAR(191) NOT NULL,
                    `applied_at` DATETIME NOT NULL,
                    PRIMARY KEY (`version`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
            );

            return;
        }

        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS schema_migrations (
                version TEXT PRIMARY KEY NOT NULL,
                applied_at TEXT NOT NULL
            )'
        );
    }

    private function isApplied(string $version): bool
    {
        $table = $this->driver === 'mysql' ? '`schema_migrations`' : 'schema_migrations';
        $stmt  = $this->pdo->prepare('SELECT 1 FROM ' . $table . ' WHERE version = ? LIMIT 1');
        $stmt->execute([$version]);

        return (bool) $stmt->fetchColumn();
    }

    private function recordApplied(string $version): void
    {
        $table = $this->driver === 'mysql' ? '`schema_migrations`' : 'schema_migrations';
        $when  = gmdate('c');
        $stmt  = $this->pdo->prepare('INSERT INTO ' . $table . ' (version, applied_at) VALUES (?, ?)');
        $stmt->execute([$version, $when]);
    }
}
