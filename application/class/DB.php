<?php

declare(strict_types=1);

namespace db;

use App\Config;
use App\MigrationRunner;
use PDO;
use PDOException;

/**
 * PDO connection factory; optional automatic migrations.
 */
final class DB
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $pdo = self::createPdo();

        if (Config::runMigrations()) {
            self::applyMigrations($pdo);
        }

        self::$pdo = $pdo;

        return self::$pdo;
    }

    /**
     * Apply pending migrations (for deploy scripts). Ignores RUN_MIGRATIONS.
     *
     * @return int Number of newly applied migration files
     */
    public static function migrate(): int
    {
        $pdo = self::$pdo ?? self::createPdo();
        $n   = self::applyMigrations($pdo);
        self::$pdo = $pdo;

        return $n;
    }

    private static function applyMigrations(PDO $pdo): int
    {
        $driver = self::driver();

        return (new MigrationRunner($pdo, $driver))->runPending();
    }

    private static function createPdo(): PDO
    {
        $cfg    = Config::all()['db'];
        $driver = $cfg['driver'] ?? 'sqlite';

        try {
            if ($driver === 'sqlite') {
                $path = (string) ($cfg['path'] ?? '');
                $dir  = dirname($path);
                if ($dir !== '' && !is_dir($dir)) {
                    if (!mkdir($dir, 0775, true) && !is_dir($dir)) {
                        throw new PDOException('Cannot create database directory: ' . $dir);
                    }
                }
                $dsn = 'sqlite:' . $path;

                return new PDO($dsn, null, null, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            }

            $host    = (string) ($cfg['host'] ?? '127.0.0.1');
            $name    = (string) ($cfg['dbname'] ?? '');
            $user    = (string) ($cfg['user'] ?? '');
            $pass    = (string) ($cfg['password'] ?? '');
            $charset = (string) ($cfg['charset'] ?? 'utf8mb4');
            $dsn     = 'mysql:host=' . $host . ';dbname=' . $name . ';charset=' . $charset;

            return new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            if (Config::debug()) {
                throw $e;
            }
            error_log('Database connection failed: ' . $e->getMessage());
            http_response_code(503);
            echo 'Сервис временно недоступен. Попробуйте позже.';
            exit;
        }
    }

    public static function driver(): string
    {
        $cfg = Config::all()['db'];

        return (string) ($cfg['driver'] ?? 'sqlite');
    }
}
