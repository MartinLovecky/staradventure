<?php

namespace Mlkali\Sa\Engine;

use Mlkali\Sa\Engine\One;
use eftec\bladeone\BladeOne;

class Blade extends BladeOne
{
    // costum @functions for view
    use One;

    public function __construct(
        private string $path = '',
        private string $viewPath = '',
        private string $compilesPath = '',
        private string $publicPath = ''
    ) {
        $this->path = dirname(__DIR__, 2) . '\\';
        $this->viewPath = $this->path . 'views';
        $this->compiledPath = $this->path . 'compiles';
        $this->publicPath = '/public';

        $this->setPath($this->viewPath, $this->compiledPath);
        $this->setBaseUrl($this->publicPath);
        //ANCHOR DEBUG ONLY FOR DEV !!!
        // $mode [MODE_AUTO, MODE_DEBUG, MODE_FAST, MODE_SLOW]
        $this->setMode(BladeOne::MODE_DEBUG);
        $this->pipeEnable = true;
    }
}
