-- MySQL / MariaDB reference schema. Prefer database/migrations/ + php bin/migrate.php for installs.

CREATE TABLE IF NOT EXISTS `task` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `text` TEXT NOT NULL,
    `status` VARCHAR(512) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_name` VARCHAR(64) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `user_name` (`user_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default admin (password: admin) — change after first login
INSERT INTO `users` (`user_name`, `password_hash`)
SELECT 'admin', '$2y$10$d80D76kBXG6e99Aja07Nw.LgVGY2NdByrBzhcBzT.d5EPG15cyMLq'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `users` WHERE `user_name` = 'admin' LIMIT 1);
