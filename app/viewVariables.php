<?php

return [
    'db' =>  $container->get(Mlkali\Sa\Database\DB::class),
    'article' => $container->get(Mlkali\Sa\Database\Entity\Article::class),
    'articleController' => $container->get(Mlkali\Sa\Controllers\ArticleController::class),
    'enc' => $container->get(Mlkali\Sa\Support\Encryption::class),
    'mailer' => $container->get(Mlkali\Sa\Support\Mailer::class),
    'pagnition' => $container->get(Mlkali\Sa\Html\Pagnition::class),
    'form' => $container->get(Mlkali\Sa\Html\Form::class),
    'validator' => $container->get(Mlkali\Sa\Support\Validator::class),
    'member' => $container->get(Mlkali\Sa\Database\Entity\Member::class),
    'memberController' => $container->get(Mlkali\Sa\Controllers\MemberController::class),
    'requestController' => $container->get(Mlkali\Sa\Controllers\RequestController::class),
    'request' => $container->get(Mlkali\Sa\Http\Request::class),
    'selector' => $selector,
    'message' => $message,
    'csrf' => $_ENV['CSRFKEY']
];