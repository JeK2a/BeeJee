<?php

declare(strict_types=1);

$base = dirname(__DIR__);
$vendor = $base . '/vendor/autoload.php';
if (is_file($vendor)) {
    require_once $vendor;
} elseif (!class_exists(\App\Config::class, false)) {
    require_once $base . '/application/lib/Config.php';
}

if (class_exists(\App\Config::class, false)) {
    $debug = \App\Config::debug();
    ini_set('display_errors', $debug ? '1' : '0');
    error_reporting($debug ? E_ALL : E_ALL & ~E_DEPRECATED & ~E_STRICT);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
}

spl_autoload_register(static function (string $class): void {
    if (str_starts_with($class, 'App\\')) {
        $rel  = str_replace('\\', '/', substr($class, strlen('App\\'))) . '.php';
        $file = dirname(__DIR__) . '/application/lib/' . $rel;
        if (is_file($file)) {
            require_once $file;
        }

        return;
    }

    if (str_starts_with($class, 'Controller_')) {
        $suffix = substr($class, strlen('Controller_'));
        $file   = dirname(__DIR__) . '/application/controllers/controller_' . strtolower($suffix) . '.php';
        if (is_file($file)) {
            require_once $file;
        }

        return;
    }

    if (str_starts_with($class, 'Model_')) {
        $suffix = substr($class, strlen('Model_'));
        $file   = dirname(__DIR__) . '/application/models/model_' . strtolower($suffix) . '.php';
        if (is_file($file)) {
            require_once $file;
        }
    }
});

require_once dirname(__DIR__) . '/application/core/model.php';
require_once dirname(__DIR__) . '/application/core/view.php';
require_once dirname(__DIR__) . '/application/core/controller.php';

\App\Session::start();
\App\SecurityHeaders::send();

require_once dirname(__DIR__) . '/application/core/route.php';
Route::start();
