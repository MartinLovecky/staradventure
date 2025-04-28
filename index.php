<?php

declare(strict_types=1);

session_start();

require(__DIR__ . '/vendor/autoload.php');

// Load environment variables.
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
$dotenv->required(['DB_NAME', 'DB_USER', 'DB_HOST', 'DB_PASS']);

// Class container with auto-wire
$container = new League\Container\Container();
$container->delegate(new League\Container\ReflectionContainer(true));

$viewController = $container->get(\Mlkali\Sa\Controllers\ViewController::class);
echo $viewController->render();
<<<<<<< HEAD
=======
//TODO -  story, admin page
//yuhzel|respect9888@gmail.com,
//Sensei|lovecky92@seznam.cz,
//visitor|visitor@gmail.com
>>>>>>> 910359bbb8bba1455894d3acb556ad9a3f3852a6
