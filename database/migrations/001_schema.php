<?php

declare(strict_types=1);

return [
    'sqlite' => [
        'CREATE TABLE IF NOT EXISTS task (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_name TEXT NOT NULL,
            email TEXT NOT NULL,
            text TEXT NOT NULL,
            status TEXT NOT NULL DEFAULT ""
        )',
        'CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_name TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL
        )',
    ],
    'mysql' => [
        'CREATE TABLE IF NOT EXISTS `task` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_name` VARCHAR(255) NOT NULL,
            `email` VARCHAR(255) NOT NULL,
            `text` TEXT NOT NULL,
            `status` VARCHAR(512) NOT NULL DEFAULT "",
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
        'CREATE TABLE IF NOT EXISTS `users` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_name` VARCHAR(64) NOT NULL,
            `password_hash` VARCHAR(255) NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `user_name` (`user_name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
    ],
];
