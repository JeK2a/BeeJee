<?php

declare(strict_types=1);

namespace App;

final class Flash
{
    private const SESSION_KEY = '_flash';

    public static function set(string $type, string $message): void
    {
        $_SESSION[self::SESSION_KEY] = ['type' => $type, 'message' => $message];
    }

    public static function success(string $message): void
    {
        self::set('success', $message);
    }

    public static function error(string $message): void
    {
        self::set('danger', $message);
    }

    /**
     * @return array{type: string, message: string}|null
     */
    public static function pull(): ?array
    {
        if (empty($_SESSION[self::SESSION_KEY]) || !is_array($_SESSION[self::SESSION_KEY])) {
            return null;
        }
        $data = $_SESSION[self::SESSION_KEY];
        unset($_SESSION[self::SESSION_KEY]);

        if (!isset($data['type'], $data['message']) || !is_string($data['type']) || !is_string($data['message'])) {
            return null;
        }

        return ['type' => $data['type'], 'message' => $data['message']];
    }
}
