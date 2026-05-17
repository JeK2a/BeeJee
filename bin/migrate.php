#!/usr/bin/env php
<?php

declare(strict_types=1);

$root = dirname(__DIR__);
require_once $root . '/vendor/autoload.php';

if (!class_exists(\App\Config::class, false)) {
    require_once $root . '/application/lib/Config.php';
}

require_once $root . '/application/lib/MigrationRunner.php';
require_once $root . '/application/class/DB.php';

$n = \db\DB::migrate();
echo $n === 0 ? "No pending migrations.\n" : "Applied {$n} migration(s).\n";
