<?php

declare(strict_types=1);

session_start();

define('ROOT_PATH',   dirname(__DIR__));
define('VIEWS_PATH',  ROOT_PATH . '/views');
define('UPLOAD_PATH', ROOT_PATH . '/uploads');

$config   = require ROOT_PATH . '/config.php';
define('BASE_URL', rtrim($config['app']['url'], '/'));

// Autoload sederhana (PSR-4 manual)
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $base   = ROOT_PATH . '/src/';

    if (strncmp($class, $prefix, strlen($prefix)) !== 0) return;

    $relative = substr($class, strlen($prefix));
    $file     = $base . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';

    if (file_exists($file)) require $file;
});

// ----- Router -----
require ROOT_PATH . '/src/router.php';
