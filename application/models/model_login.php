<?php

declare(strict_types=1);

use db\DB;

require_once dirname(__DIR__) . '/class/DB.php';

class Model_Login implements Model
{
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

    public function verifyCredentials(string $userName, string $password): bool
    {
        $stmt = $this->db->prepare('SELECT password_hash FROM users WHERE user_name = ? LIMIT 1');
        $stmt->execute([$userName]);
        $row = $stmt->fetch();
        if ($row === false || empty($row['password_hash'])) {
            return false;
        }

        return password_verify($password, (string) $row['password_hash']);
    }
}
