<?php

//REVIEW - FOR DEBUG ONLY
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

session_start();

require 'vendor/autoload.php';

// Load environment variables.
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dotenv->required(['DB_NAME', 'DB_USER', 'DB_HOST', 'DB_PASS']);

//Class container
$container = new League\Container\Container();
//$container->addServiceProvider(new Mlkali\Sa\Support\DependencyProvider());
require_once 'app/container.php';

// Set the base URL for the blade template engine.
$blade = $container->get('blade');
$blade->setBaseUrl('/public');

//Request configuration
$request = $container->get('request');
$request->getRequest();

$enc = $container->get('enc');
$db = $container->get('db');
$mailer = $container->get('mailer');
$pagnition = $container->get('pagnition');
$form = $container->get('form');
$validator = $container->get('validator');

// Retrieve the view name from the request URI.
$selector = $container->get('selector');
$selector->getViewName(require_once(__DIR__ . '/app/allowedViews.php'));

// Messages are allways inputed in raw string 
$message = $container->get('message');
$message->getQueryMessage();

// Article handling
$article = $container->get('article');
$articleController = $container->get('articleController');

//FIXME - These classesneed to be tested / completed
$member = $container->get('member');
//NOTE - not exactly sure wich classes I will need to put iside container
$memberController = $container->get('memberController');
//memberController->recallUser();
//FIXME - submitRegister doest fillsecondary table (info) !important to fix.
//NOTE - bcs class member is currently not implemented there is lot of issues with requestController
//$requestController = $container->get('requestController');

//FIXME - view Variables are *** bcs I need fix member

echo $blade->run($selector->viewName);
?>