<?php

namespace Mlkali\Sa\Support;

class Selector
{

    public function __construct(
        public string $viewName = 'index',
        public string $component = '',
        public ?string $action = null,
        public ?string $article = null,
        public ?string $page = null,
        public ?string $title = null,
        public ?string $queryMsg = null,
        public ?string $queryAction = null,
        public ?string $queryID = null,
        public ?string $queryToken = null,
        public ?string $articleID = null,
        private array $queryValues = [],
        private ?array $url = null,
    ) {
        // normal url values /action/article/page
        $this->url = explode('/', trim(str_replace(['<', '>', '!', '@', '$'], '', urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)))));
        array_shift($this->url);
        $this->action = $this->url[0] ?? $this->action;
        $this->article = $this->url[1] ?? $this->article;
        $this->page = $this->url[2] ?? $this->page;
        $this->articleID = isset($this->article) && isset($this->page) ? $this->article . '|'. $this->page : $this->articleID;
        // url Query values
        parse_str($_SERVER['QUERY_STRING'], $this->queryValues);
        $this->queryMsg = $this->queryValues['message'] ?? $this->queryMsg;
        $this->queryID = $this->queryValues['id'] ?? $this->queryID;
        $this->queryAction = $this->queryValues['action'] ?? $this->queryAction;
        $this->queryToken = $this->queryValues['token'] ?? $this->queryToken;
    }

    public function getViewName(array $allowed): void
    {
        if (in_array(strtolower($this->action), $allowed)) {

            match ($this->action) {
                '', 'intro', 'login', 'newpassword', 'register', 'reset', 'storylist', 'terms', 'vop', 'updatemember' => $this->viewName = 'index', $this->title = 'SA | ' . $this->action,
                'show' => [$this->viewName = 'app', $this->component = 'articles', $this->title = 'SA | ' . $this->action . ' | ' . $this->article ?? $this->article],
                ['update', 'delete', 'create'] => [$this->viewName = 'app', $this->component = 'editor', $this->title = 'SA | ' . $this->action . ' | ' . $this->article ?? $this->article],
                default => [$this->viewName = 'app', $this->component = $this->action, $this->title = 'SA | ' . $this->action . ' | ' . $this->article ?? $this->article]
            };
        } else {
            $this->component = 'notfound';
            $this->viewName = 'app';
            $this->title = 'SA | 404';
        }
    }

    public function debug(): void
    {
        ini_set('display_startup_errors', 1);
        ini_set('display_errors', 1);
        error_reporting(-1);
    }
}
