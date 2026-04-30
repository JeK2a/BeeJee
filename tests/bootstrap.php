<?php

declare(strict_types=1);

$_ENV['APP_DEBUG']   = '1';
$_ENV['DB_DRIVER']   = 'sqlite';
$_ENV['DB_PATH']     = sys_get_temp_dir() . '/tasks_test_' . uniqid('', true) . '.sqlite';
putenv('APP_DEBUG=1');
putenv('DB_DRIVER=sqlite');
putenv('DB_PATH=' . $_ENV['DB_PATH']);

require dirname(__DIR__) . '/vendor/autoload.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
