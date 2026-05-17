<?php

declare(strict_types=1);

return [
    'sqlite' => [],
    'mysql'  => [],
    'after'  => static function (\PDO $pdo, string $driver): void {
        $usersTable = $driver === 'mysql' ? '`users`' : 'users';
        $stmt       = $pdo->query('SELECT COUNT(*) FROM ' . $usersTable);
        if ((int) $stmt->fetchColumn() > 0) {
            return;
        }

        $hash = password_hash('admin', PASSWORD_DEFAULT);
        if ($driver === 'mysql') {
            $ins = $pdo->prepare('INSERT IGNORE INTO `users` (`user_name`, `password_hash`) VALUES (?, ?)');
            $ins->execute(['admin', $hash]);

            return;
        }

        $ins = $pdo->prepare('INSERT OR IGNORE INTO users (user_name, password_hash) VALUES (?, ?)');
        $ins->execute(['admin', $hash]);
    },
];
