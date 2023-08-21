<?php

session_start();

require 'vendor/autoload.php';
// Load environment variables.
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dotenv->required(['DB_NAME', 'DB_USER', 'DB_HOST', 'DB_PASS']);
// Class container with auto-wire
$container = new League\Container\Container();
$container->delegate(new League\Container\ReflectionContainer(true));

// Sets viewName and $queryValues this will be propably moved
// Get &message=encrypted($msg)
$message = $container->get(Mlkali\Sa\Support\Messages::class);
$message->getQueryMessage();

$viewController = $container->get(Mlkali\Sa\Controllers\ViewController::class);

echo $viewController->view();    