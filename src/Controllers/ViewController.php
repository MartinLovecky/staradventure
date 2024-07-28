<?php

namespace Mlkali\Sa\Controllers;

use Mlkali\Sa\Support\ViewModel;

//TODO - this should handle views not just render them
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
