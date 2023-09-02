<?php
//only for dev tools/php-cs-fixer/vendor/bin/php-cs-fixer fix src
session_start();

require 'vendor/autoload.php';
// Load environment variables.
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dotenv->required(['DB_NAME', 'DB_USER', 'DB_HOST', 'DB_PASS']);
// Class container with auto-wire
$container = new League\Container\Container();
$container->delegate(new League\Container\ReflectionContainer(true));

$viewController = $container->get(Mlkali\Sa\Controllers\ViewController::class);

echo $viewController->view();