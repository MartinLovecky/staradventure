<?php

namespace Mlkali\Sa\Support;

class Selector
{
    public function __construct(
        public ?string $action = null,
        public ?string $article = null,
        public ?string $page = null,
        public string $title = '',
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
        $this->articleID = isset($this->article) && isset($this->page) ? $this->article . '|' . $this->page : $this->articleID;
        // url Query values
        isset($_SERVER['QUERY_STRING']) ? parse_str($_SERVER['QUERY_STRING'], $this->queryValues) : null;
        $this->queryMsg = $this->queryValues['message'] ?? $this->queryMsg;
        $this->queryID = $this->queryValues['id'] ?? $this->queryID;
        $this->queryAction = $this->queryValues['action'] ?? $this->queryAction;
        $this->queryToken = $this->queryValues['token'] ?? $this->queryToken;
    }

    public function debug(): void
    {
        ini_set('display_startup_errors', 1);
        ini_set('display_errors', 1);
        error_reporting(-1);
    }
}
