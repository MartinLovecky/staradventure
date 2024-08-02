<?php
// tools/php-cs-fixer/vendor/bin/php-cs-fixer fix src
declare(strict_types=1);
session_start();
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

require 'vendor/autoload.php';
// Load environment variables.
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
$dotenv->required(['DB_NAME', 'DB_USER', 'DB_HOST', 'DB_PASS']);
// Class container with auto-wire
$container = new League\Container\Container();
$container->delegate(new League\Container\ReflectionContainer(true));

$viewController = $container->get(\Mlkali\Sa\Engine\ViewController::class);

echo $viewController->view();
