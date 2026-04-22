<?php

declare(strict_types=1);

namespace db;

use App\Config;
use PDO;
use PDOException;

/**
 * PDO connection factory with optional schema bootstrap.
 */
final class DB
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

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
                $pdo = new PDO($dsn, null, null, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
                self::ensureSqliteSchema($pdo);
            } else {
                $host    = (string) ($cfg['host'] ?? '127.0.0.1');
                $name    = (string) ($cfg['dbname'] ?? '');
                $user    = (string) ($cfg['user'] ?? '');
                $pass    = (string) ($cfg['password'] ?? '');
                $charset = (string) ($cfg['charset'] ?? 'utf8mb4');
                $dsn     = 'mysql:host=' . $host . ';dbname=' . $name . ';charset=' . $charset;
                $pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
                self::ensureMysqlSchema($pdo);
            }
        } catch (PDOException $e) {
            if (Config::debug()) {
                throw $e;
            }
            error_log('Database connection failed: ' . $e->getMessage());
            http_response_code(503);
            echo 'Сервис временно недоступен. Попробуйте позже.';
            exit;
        }

        self::$pdo = $pdo;

        return self::$pdo;
    }

    private static function ensureSqliteSchema(PDO $pdo): void
    {
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS task (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_name TEXT NOT NULL,
                email TEXT NOT NULL,
                text TEXT NOT NULL,
                status TEXT NOT NULL DEFAULT ""
            )'
        );

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_name TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL
            )'
        );

        $stmt = $pdo->query('SELECT COUNT(*) FROM users');
        if ((int) $stmt->fetchColumn() === 0) {
            $hash = password_hash('admin', PASSWORD_DEFAULT);
            $ins = $pdo->prepare('INSERT INTO users (user_name, password_hash) VALUES (?, ?)');
            $ins->execute(['admin', $hash]);
        }
    }

    private static function ensureMysqlSchema(PDO $pdo): void
    {
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS `task` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_name` VARCHAR(255) NOT NULL,
                `email` VARCHAR(255) NOT NULL,
                `text` TEXT NOT NULL,
                `status` VARCHAR(512) NOT NULL DEFAULT "",
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS `users` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_name` VARCHAR(64) NOT NULL,
                `password_hash` VARCHAR(255) NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `user_name` (`user_name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );

        $stmt = $pdo->query('SELECT COUNT(*) FROM `users`');
        if ((int) $stmt->fetchColumn() === 0) {
            $hash = password_hash('admin', PASSWORD_DEFAULT);
            $ins = $pdo->prepare('INSERT INTO `users` (`user_name`, `password_hash`) VALUES (?, ?)');
            $ins->execute(['admin', $hash]);
        }
    }

    public static function driver(): string
    {
        $cfg = Config::all()['db'];

        return (string) ($cfg['driver'] ?? 'sqlite');
    }
}
