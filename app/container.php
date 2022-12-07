<?php

use eftec\bladeone\BladeOne;

$container->add('enc', Mlkali\Sa\Support\Encryption::class);
$container->add('db', Mlkali\Sa\Database\DB::class)
    ->addArgument($_ENV);
$container->add('blade', function () {
    return new BladeOne($_SERVER['DOCUMENT_ROOT'] . '/views', $_SERVER['DOCUMENT_ROOT'] . '/cache', BladeOne::MODE_AUTO);
});
$container->add('selector', Mlkali\Sa\Support\Selector::class);
$container->add('pagnition', Mlkali\Sa\Html\Pagnition::class)
    ->addArgument($container->get('selector'));
$container->add('request', Mlkali\Sa\Http\Request::class);
$container->add('message', Mlkali\Sa\Support\Messages::class)
    ->addArgument($container->get('selector'))
    ->addArgument($container->get('enc'));
$container->add('mailer', Mlkali\Sa\Support\Mailer::class);
$container->add('member', Mlkali\Sa\Database\Entity\Member::class);
$container->add('memberController', Mlkali\Sa\Controllers\MemberController::class)
    ->addArgument($container->get('member'));
$container->add('article', Mlkali\Sa\Database\Entity\Article::class)
    ->addArgument($container->get('db'))
    ->addArgument($container->get('selector'));
$container->add('validator', Mlkali\Sa\Support\Validator::class)
    ->addArgument($container->get('enc'))
    ->addArgument($container->get('memberController'));
$container->add('requestController', Mlkali\Sa\Controllers\RequestController::class)
    ->addArgument($container->get('request'))
    ->addArgument($container->get('db'))
    ->addArgument($container->get('mailer'))
    ->addArgument($container->get('validator'))
    ->addArgument($container->get('memberController'))
    ->addArgument($container->get('enc'));
$container->add('articleController', Mlkali\Sa\Controllers\ArticleController::class)
    ->addArgument($container->get('request'))
    ->addArgument($container->get('article'));
$container->add('form', Mlkali\Sa\Html\Form::class)
    ->addArgument($container->get('blade'));