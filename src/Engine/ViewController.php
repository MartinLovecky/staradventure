<?php

namespace Mlkali\Sa\Engine;

use Mlkali\Sa\Engine\ViewModel;

class ViewController
{
    public function __construct(private ViewModel $viewModel)
    {
    }

    public function view(): string
    {
        return $this->viewModel->render();
    }
}
