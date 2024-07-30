<?php

namespace Mlkali\Sa\Engine;

use Mlkali\Sa\Engine\ViewModel;

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
