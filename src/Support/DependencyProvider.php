<?php

namespace Mlkali\Sa\Support;

use League\Container\ServiceProvider\AbstractServiceProvider;

class DependencyProvider extends AbstractServiceProvider{
    protected $container;
    protected $identifier;

    public function provides(string $id): bool
    {
        $services = [
            Mlkali\Sa\Support\Encryption::class,
            Mlkali\Sa\Database\DB::class,
            Mlkali\Sa\Support\Selector::class,
            Mlkali\Sa\Html\Pagnition::class,
            Mlkali\Sa\Http\Request::class,
            Mlkali\Sa\Support\Messages::class,
            Mlkali\Sa\Support\Mailer::class,
            Mlkali\Sa\Database\Entity\Member::class,
            Mlkali\Sa\Controllers\MemberController::class,
            Mlkali\Sa\Database\Entity\Article::class,
            Mlkali\Sa\Support\Validator::class,
            Mlkali\Sa\Controllers\RequestController::class,
            Mlkali\Sa\Controllers\ArticleController::class,
            Mlkali\Sa\Html\Form::class,
        ];

        return in_array($id, $services);
    }

    public function register(): void
    {
        $container = $this->getContainer();
        
        $container->add(Mlkali\Sa\Support\Encryption::class);
        $container->add(Mlkali\Sa\Database\DB::class);
        $container->add(Mlkali\Sa\Support\Selector::class);
        $container->add(Mlkali\Sa\Html\Pagnition::class)
            ->addArgument($container->get(Mlkali\Sa\Support\Selector::class));
        $container->add(Mlkali\Sa\Http\Request::class);
        $container->add(Mlkali\Sa\Support\Messages::class)
            ->addArgument($container->get(Mlkali\Sa\Support\Selector::class))
            ->addArgument($container->get(Mlkali\Sa\Support\Encryption::class));
        $container->add(Mlkali\Sa\Support\Mailer::class);
        $container->add(Mlkali\Sa\Database\Entity\Member::class);
        $container->add(Mlkali\Sa\Controllers\MemberController::class)
            ->addArgument($container->get(Mlkali\Sa\Database\DB::class))
            ->addArgument($container->get(Mlkali\Sa\Support\Encryption::class))
            ->addArgument($container->get(Mlkali\Sa\Database\Entity\Member::class));
        $container->add(Mlkali\Sa\Database\Entity\Article::class)
            ->addArgument($container->get(Mlkali\Sa\Database\DB::class))
            ->addArgument($container->get(Mlkali\Sa\Support\Selector::class));
        $container->add(Mlkali\Sa\Support\Validator::class)
            ->addArgument($container->get(Mlkali\Sa\Support\Encryption::class))
            ->addArgument($container->get(Mlkali\Sa\Controllers\MemberController::class));
        $container->add(Mlkali\Sa\Controllers\RequestController::class)
            ->addArgument($container->get(Mlkali\Sa\Http\Request::class))
            ->addArgument($container->get(Mlkali\Sa\Database\DB::class))
            ->addArgument($container->get(Mlkali\Sa\Support\Mailer::class))
            ->addArgument($container->get(Mlkali\Sa\Support\Validator::class))
            ->addArgument($container->get(Mlkali\Sa\Controllers\MemberController::class))
            ->addArgument($container->get(Mlkali\Sa\Support\Encryption::class));
        $container->add(Mlkali\Sa\Controllers\ArticleController::class)
            ->addArgument($container->get(Mlkali\Sa\Http\Request::class))
            ->addArgument($container->get(Mlkali\Sa\Database\Entity\Article::class));
}

}