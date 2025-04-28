<?php

declare(strict_types=1);

namespace Mlkali\Sa\Engine;

use Mlkali\Sa\Engine\One;
use eftec\bladeone\BladeOne;

class Blade extends BladeOne
{
    // costum @functions for view
    use One;

    private string $path = '';
    private string $viewPath = '';
    private string $compilesPath = '';
    private string $publicPath = '';

    public function __construct()
    {
        $this->path = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR;
        $this->viewPath = $this->path . 'views';
        $this->compiledPath = $this->path . 'compiles';
        $this->publicPath = DIRECTORY_SEPARATOR . 'public';

        $this->setPath($this->viewPath, $this->compiledPath);
        $this->setBaseUrl($this->publicPath);
        //[MODE_AUTO, MODE_DEBUG, MODE_FAST, MODE_SLOW]
        $this->setMode(BladeOne::MODE_AUTO);
        $this->pipeEnable = true;
    }
}
