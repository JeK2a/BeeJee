<?php

declare(strict_types=1);

use db\DB;

require_once dirname(__DIR__) . '/class/DB.php';

class Model_ChangePassword implements Model
{
    private const MIN_PASSWORD_LENGTH = 8;

    private \PDO $db;

    public function __construct()
    {
        $this->db = DB::getConnection();
    }

    public function get_data($params)
    {
        return [];
    }

    public function set_data($data)
    {
    }

    /**
     * @return string empty string on success, or error code: mismatch|weak|current|not_found|db
     */
    public function changePassword(string $userName, string $current, string $new, string $confirm): string
    {
        if ($new !== $confirm) {
            return 'mismatch';
        }
        if (mb_strlen($new, 'UTF-8') < self::MIN_PASSWORD_LENGTH) {
            return 'weak';
        }

        $stmt = $this->db->prepare('SELECT password_hash FROM users WHERE user_name = ? LIMIT 1');
        $stmt->execute([$userName]);
        $row = $stmt->fetch();
        if ($row === false || empty($row['password_hash'])) {
            return 'not_found';
        }

        if (!password_verify($current, (string) $row['password_hash'])) {
            return 'current';
        }

        if (password_verify($new, (string) $row['password_hash'])) {
            return 'same';
        }

        $hash = password_hash($new, PASSWORD_DEFAULT);
        $upd  = $this->db->prepare('UPDATE users SET password_hash = ? WHERE user_name = ?');

        try {
            $upd->execute([$hash, $userName]);
        } catch (\PDOException $e) {
            error_log('Password update failed: ' . $e->getMessage());

            return 'db';
        }

        return '';
    }
}
