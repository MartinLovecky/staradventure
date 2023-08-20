<?php

namespace Mlkali\Sa\ViewControllers;

use Mlkali\Sa\ViewModels\IndexModel;

class IndexController
{
    public function __construct(private IndexModel $viewModel)
    {
    }

    public function index(): string
    {
        return $this->viewModel->render();
    }

    public function intro(): string
    {
        return $this->viewModel->render('intro');
    }

    public function register(): string
    {
        return $this->viewModel->render('register');
    }

    public function login(): string
    {
        return $this->viewModel->render('login');
    }

    public function reset(): string
    {
        return $this->viewModel->render('reset');
    }

    public function notFound(): string
    {
        return $this->viewModel->render('notFound');
    }

    public function storylist(): string 
    {
        return $this->viewModel->render('storylist');
    }

    public function terms(): string
    {
        return $this->viewModel->render('terms');
    }

    public function vop(): string
    {
        return $this->viewModel->render('vop');
    }
}