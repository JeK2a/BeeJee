<?php

declare(strict_types=1);

namespace App;

final class LoginRateLimiter
{
    private const SESSION_KEY = '_login_rate';
    private const MAX_ATTEMPTS = 5;
    private const LOCK_SECONDS = 900;

    public static function isLocked(): bool
    {
        $state = $_SESSION[self::SESSION_KEY] ?? null;
        if (!is_array($state)) {
            return false;
        }
        $until = (int) ($state['locked_until'] ?? 0);
        if ($until <= 0) {
            return false;
        }
        if (time() >= $until) {
            unset($_SESSION[self::SESSION_KEY]);

            return false;
        }

        return true;
    }

    public static function secondsRemaining(): int
    {
        $state = $_SESSION[self::SESSION_KEY] ?? [];
        $until = (int) ($state['locked_until'] ?? 0);

        return max(0, $until - time());
    }

    public static function recordFailure(): void
    {
        $state = $_SESSION[self::SESSION_KEY] ?? ['failures' => 0];
        $failures = (int) ($state['failures'] ?? 0) + 1;
        if ($failures >= self::MAX_ATTEMPTS) {
            $_SESSION[self::SESSION_KEY] = [
                'failures'     => $failures,
                'locked_until' => time() + self::LOCK_SECONDS,
            ];

            return;
        }
        $_SESSION[self::SESSION_KEY] = ['failures' => $failures, 'locked_until' => 0];
    }

    public static function reset(): void
    {
        unset($_SESSION[self::SESSION_KEY]);
    }
}
