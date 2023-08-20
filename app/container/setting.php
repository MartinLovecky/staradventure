<?php

// simple Router
$container = new League\Container\Container();
$container->delegate(new League\Container\ReflectionContainer(true));
$selector = $container->get(Mlkali\Sa\Support\Selector::class);

$routes = [
    '' => Mlkali\Sa\ViewControllers\IndexController::class.'@index',
    'index' => Mlkali\Sa\ViewControllers\IndexController::class.'@index',
    'user',
    '404' => Mlkali\Sa\ViewControllers\IndexController::class.'@notFound',
    'storylist' => Mlkali\Sa\ViewControllers\IndexController::class.'@storylist',
    'show',
    'register' => Mlkali\Sa\ViewControllers\IndexController::class.'@register',
    'login' => Mlkali\Sa\ViewControllers\IndexController::class.'@login',
    'intro' => Mlkali\Sa\ViewControllers\IndexController::class.'@intro',
    'newpassword',
    'updatemember',
    'reset' => Mlkali\Sa\ViewControllers\IndexController::class.'@reset',
    'terms' => Mlkali\Sa\ViewControllers\IndexController::class.'@terms',
    'vop' => Mlkali\Sa\ViewControllers\IndexController::class.'@vop',
    'usertable',
    'editor'
];

$route = $selector->action;

if(array_key_exists($route, $routes))
{
    $controllerAction = $routes[$route];
    [$controllerName, $action] = explode('@', $controllerAction);
    $controller = $container->get($controllerName);

    echo $controller->$action();
}
else
{
    $action = $routes['404'];
    [$controllerName, $action] = explode('@', $action);
    $controller = $container->get($controllerName);

    echo $controller->$action();
}