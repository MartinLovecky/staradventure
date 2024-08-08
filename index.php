<?php
// tools/php-cs-fixer/vendor/bin/php-cs-fixer fix src
declare(strict_types=1);
session_start();
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

require(__DIR__ . '/vendor/autoload.php');

// Load environment variables.
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
$dotenv->required(['DB_NAME', 'DB_USER', 'DB_HOST', 'DB_PASS']);
// Class container with auto-wire
$container = new League\Container\Container();
$container->delegate(new League\Container\ReflectionContainer(true));

$repo = $container->get(\Mlkali\Sa\Database\Fluent::class);
// Insert data into the cards table
// $repo->query->insertInto('cards')->values([
//     'img_src' => "@asset('img/cards/allwin.png')",
//     'title' => 'Allwin',
//     'link' => '/show/allwin/1#story',
//     'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Officiis eveniet placeat dolorem aliquam, possimus saepe nulla quasi architecto ratione sequi blanditiis, nostrum quos aut inventore molestias! Officiis sed distinctio repudiandae.',
//     'author_img' => "@asset('img/avatars/sensei400x4400.jpeg')",
//     'author_link' => '/member/sensei',
//     'author_name' => '@Sensei'
// ])->execute();

//$cardsData = $repo->query->from('cards')->fetchAll();
//$x = $repo->query->from('cards')->fetch();
//$p = $repo->query->update('cards')->set(['author_img' => "img/avatars/sensei400x400.png", 'img_src' => "img/cards/allwin.png"])->where('author_name', '@Sensei')->execute();
//dd($p, $x);
//dd($cardsData);


// COULD MANUALY ACTIVATE BUT I want test mailing
$viewController = $container->get(\Mlkali\Sa\Engine\ViewController::class);

echo $viewController->view();
//TODO -  fix editor|story|