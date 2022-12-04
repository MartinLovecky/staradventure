<?php

//REVIEW - FOR DEBUG ONLY
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

use eftec\bladeone\BladeOne;

session_start();

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
$dotenv->required(['DB_NAME', 'DB_USER', 'DB_HOST', 'DB_PASS']);
$enc = new Mlkali\Sa\Support\Encryption();
$db = new Mlkali\Sa\Database\DB();
$blade = new BladeOne(__DIR__ . '/views', __DIR__ . '/cache', BladeOne::MODE_AUTO);
$blade->setBaseUrl('/public');
$selector = new Mlkali\Sa\Support\Selector();
$selector->getViewName(require_once(__DIR__ . '/app/allowedViews.php'));
$pagnition = new Mlkali\Sa\Html\Pagnition($selector);
$request = new Mlkali\Sa\Http\Request();
$request->getRequest();
$message = new Mlkali\Sa\Support\Messages($selector, $enc);
$message->getQueryMessage($selector->fristQueryValue);
$mailer = new Mlkali\Sa\Support\Mailer();
$member = new Mlkali\Sa\Database\User\Member($db, $enc);
$member->recallUser();
$article = new Mlkali\Sa\Database\Entity\Article($db, $selector);
$validator = new Mlkali\Sa\Support\Validator($enc, $member);
$requestController = new Mlkali\Sa\Controllers\RequestController($request, $db, $mailer, $validator, $member, $enc);
$articleController = new Mlkali\Sa\Controllers\ArticleController($request, $article);
$form = new Mlkali\Sa\Html\Form($blade);

//FIXME - submitRegister doest fillsecondary table (info) !important to fix
//TODO - some Member class methods are really convoluted ......

echo $blade->run($selector->viewName, require_once(__DIR__ . '/app/viewVariables.php'));

?>