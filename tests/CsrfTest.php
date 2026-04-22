<?php

declare(strict_types=1);

namespace Tests;

use App\Csrf;
use PHPUnit\Framework\TestCase;

final class CsrfTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function testTokenIsStableWithinSession(): void
    {
        $a = Csrf::token();
        $b = Csrf::token();
        self::assertSame($a, $b);
        self::assertGreaterThan(20, strlen($a));
    }

    public function testValidateAcceptsCorrectToken(): void
    {
        $t = Csrf::token();
        self::assertTrue(Csrf::validate($t));
    }

    public function testValidateRejectsWrongToken(): void
    {
        Csrf::token();
        self::assertFalse(Csrf::validate('wrong'));
    }
}
