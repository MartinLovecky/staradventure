<?php

use eftec\bladeone\BladeOne;

session_start();

require 'vendor/autoload.php';
// Load environment variables.
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dotenv->required(['DB_NAME', 'DB_USER', 'DB_HOST', 'DB_PASS']);
// Class container with autowire
$container = new League\Container\Container();
$container->delegate(new League\Container\ReflectionContainer(true));
// Blade Template engine
$blade = new BladeOne(__DIR__ . '/views', __DIR__ . '/compiles', BladeOne::MODE_AUTO);
$blade->setBaseUrl('/public');
// Sets viewName and $queryValues
$selector = $container->get(Mlkali\Sa\Support\Selector::class);
$selector->getViewName(require_once(__DIR__ . '/app/allowedViews.php'));
// Get &message=encrypted($msg)
$message = $container->get(Mlkali\Sa\Support\Messages::class);
$message->getQueryMessage();

echo $blade->run($selector->viewName, require_once(__DIR__ . '/app/viewVariables.php'));