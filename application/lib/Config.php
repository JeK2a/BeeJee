<?php

declare(strict_types=1);

namespace App;

final class Config
{
    /** @var array<string, mixed>|null */
    private static ?array $cached = null;

    /**
     * @return array{debug: bool, db: array<string, mixed>}
     */
    public static function all(): array
    {
        if (self::$cached !== null) {
            return self::$cached;
        }

        self::loadEnvFile();

        $debug = filter_var(self::env('APP_DEBUG', '0'), FILTER_VALIDATE_BOOLEAN);

        self::$cached = [
            'debug' => $debug,
            'db'    => self::dbConfig(),
        ];

        return self::$cached;
    }

    public static function debug(): bool
    {
        return (bool) self::all()['debug'];
    }

    public static function runMigrations(): bool
    {
        self::all();

        return filter_var(self::env('RUN_MIGRATIONS', '1'), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return array<string, mixed>
     */
    private static function dbConfig(): array
    {
        $driver = strtolower(self::env('DB_DRIVER', 'sqlite'));

        if ($driver === 'mysql') {
            return [
                'driver'   => 'mysql',
                'host'     => self::env('DB_HOST', '127.0.0.1'),
                'dbname'   => self::env('DB_NAME', 'tasks'),
                'user'     => self::env('DB_USER', 'root'),
                'password' => self::env('DB_PASSWORD', ''),
                'charset'  => self::env('DB_CHARSET', 'utf8mb4'),
            ];
        }

        $defaultPath = dirname(__DIR__, 2) . '/storage/database.sqlite';

        return [
            'driver'  => 'sqlite',
            'path'    => self::env('DB_PATH', $defaultPath),
            'charset' => 'utf8',
        ];
    }

    private static function env(string $key, string $default = ''): string
    {
        $v = $_ENV[$key] ?? getenv($key);
        if ($v === false || $v === '') {
            return $default;
        }

        return (string) $v;
    }

    private static function loadEnvFile(): void
    {
        $path = dirname(__DIR__, 2) . '/.env';
        if (!is_readable($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (!str_contains($line, '=')) {
                continue;
            }
            [$name, $value] = explode('=', $line, 2);
            $name  = trim($name);
            $value = trim($value, " \t\"'");
            if ($name === '') {
                continue;
            }
            $_ENV[$name]   = $value;
            putenv($name . '=' . $value);
        }
    }
}
