<?php

declare(strict_types=1);

namespace App;

final class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    public static function token(): string
    {
        if (empty($_SESSION[self::SESSION_KEY]) || !is_string($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    public static function field(): string
    {
        $t = htmlspecialchars(self::token(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return '<input type="hidden" name="_csrf" value="' . $t . '">';
    }

    public static function validate(?string $token): bool
    {
        $expected = $_SESSION[self::SESSION_KEY] ?? null;
        if (!is_string($expected) || $expected === '' || !is_string($token)) {
            return false;
        }

        return hash_equals($expected, $token);
    }
}
