<?php

declare(strict_types=1);

namespace Tests;

use App\LoginRateLimiter;
use PHPUnit\Framework\TestCase;

final class LoginRateLimiterTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function testLocksAfterFailures(): void
    {
        self::assertFalse(LoginRateLimiter::isLocked());
        for ($i = 0; $i < 5; $i++) {
            LoginRateLimiter::recordFailure();
        }
        self::assertTrue(LoginRateLimiter::isLocked());
        LoginRateLimiter::reset();
        self::assertFalse(LoginRateLimiter::isLocked());
    }
}
