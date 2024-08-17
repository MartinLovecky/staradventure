<?php

declare(strict_types=1);

session_start();

require(__DIR__ . '/vendor/autoload.php');

// Load environment variables.
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
$dotenv->required(['DB_NAME', 'DB_USER', 'DB_HOST', 'DB_PASS']);

if ($_ENV['MODE'] == 'dev') {
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('html_errors', 1);
    ini_set('error_log', '/logs/php_errors.log');
    error_reporting(E_ALL);
}

// Class container with auto-wire
$container = new League\Container\Container();
$container->delegate(new League\Container\ReflectionContainer(true));

$viewController = $container->get(\Mlkali\Sa\Engine\ViewController::class);

echo $viewController->view();
//TODO -  story, admin page