<?php

namespace Mlkali\Sa\Support;

class Selector
{

    public function __construct(
        public string $viewName = '',
        public string $viewPage = '',
        public ?string $action = null,
        public ?string $article = null,
        public $page = null,
        public ?string $title = null,
        public ?string $queryAction = null,
        public ?string $fristQueryValue = null,
        public ?string $secondQueryValue = null,
        public ?string $thirdQueryValue = null,
        private array $queryValues = [],
        private array $url = []
    ) {
        //url query values
        $this->queryValues = isset($_SERVER['QUERY_STRING']) ? explode('=', $_SERVER['QUERY_STRING']) : $this->queryValues;
        $this->queryAction = $this->queryValues[0] ?? $this->queryAction;
        $this->fristQueryValue = !empty($this->queryValues[0]) ? filter_input(INPUT_GET, trim($this->queryValues[0]), FILTER_SANITIZE_FULL_SPECIAL_CHARS) : $this->fristQueryValue;
        $this->secondQueryValue = isset($this->queryValues[1]) ? $this->getQueryValue($this->queryValues[1]) : $this->secondQueryValue;
        $this->thirdQueryValue =  isset($this->queryValues[2]) ? $this->getQueryValue($this->queryValues[2]) : $this->thirdQueryValue;
        // normal url values /action/article/page
        $this->url = explode('/', trim(str_replace(['<', '>', '!', '@', '$'], '', urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)))));
        $this->action = isset($this->url[1]) ? $this->url[1] : $this->action;
        $this->article = isset($this->url[2]) ? $this->url[2] : $this->article;
        $this->page = isset($this->url[3]) ? $this->url[3] : $this->page;
    }

    /**
     * sets View name for bladeone to load example [viewName.blade.php], also you can set up title for the view
     *
     * @param array $allowed
     * @return void
     */
    public function getViewName(array $allowed): void
    {
        if (in_array(strtolower($this->action), $allowed)) {
            switch ($this->action) {
                case '':
                case 'intro':
                case 'login':
                case 'newpassword':
                case 'register':
                case 'reset':
                case 'storylist':
                case 'terms':
                case 'vop':
                    $this->viewName = 'index';
                    $this->title = 'SA | ' . $this->action;
                    break;
                case 'show':
                    $this->viewName = 'app';
                    $this->viewPage = 'articles';
                    $this->title = 'SA | ' . $this->action . ' | ' . $this->article ?? $this->article;
                    break;
                case 'update':
                case 'delete':
                case 'create':
                    $this->viewName = 'app';
                    $this->viewPage = 'editor';
                    $this->title = 'SA | ' . $this->action . ' | ' . $this->article ?? $this->article;
                    break;
                case $this->action:
                    $this->viewName = 'app';
                    $this->viewPage = $this->action;
                    $this->title = 'SA | ' . $this->action . ' | ' . $this->article ?? $this->article;
                    break;
            }
        } else {
            $this->viewPage = 'notfound';
            $this->viewName = 'app';
            $this->title = 'SA | 404';
        }
    }

    /**
     * gets QUERY_STRING value after &x=value
     *
     * @param string $value
     * @return mixed
     */
    private function getQueryValue(string $value)
    {
        return filter_input(INPUT_GET, trim(str_replace('&', '', strpbrk($value, '&'))), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
}
