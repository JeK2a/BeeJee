<?php

declare(strict_types=1);

namespace Tests;

use db\DB;
use PHPUnit\Framework\TestCase;

final class ModelChangePasswordTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
        require_once dirname(__DIR__) . '/application/core/model.php';
        require_once dirname(__DIR__) . '/application/class/DB.php';
        require_once dirname(__DIR__) . '/application/models/model_changepassword.php';

        $pdo = DB::getConnection();
        $hash = password_hash('admin', PASSWORD_DEFAULT);
        $pdo->prepare('UPDATE users SET password_hash = ? WHERE user_name = ?')->execute([$hash, 'admin']);
    }

    public function testChangePasswordSuccess(): void
    {
        $m = new \Model_ChangePassword();
        self::assertSame('', $m->changePassword('admin', 'admin', 'newpass12', 'newpass12'));

        $m2 = new \Model_ChangePassword();
        self::assertSame('', $m2->changePassword('admin', 'newpass12', 'secondpass9', 'secondpass9'));
    }

    public function testMismatchAndWeak(): void
    {
        $m = new \Model_ChangePassword();
        self::assertSame('mismatch', $m->changePassword('admin', 'admin', 'newpass12', 'other'));
        self::assertSame('weak', $m->changePassword('admin', 'admin', 'short', 'short'));
    }

    public function testWrongCurrent(): void
    {
        $m = new \Model_ChangePassword();
        self::assertSame('current', $m->changePassword('admin', 'wrongpassword', 'newpass12', 'newpass12'));
    }
}
