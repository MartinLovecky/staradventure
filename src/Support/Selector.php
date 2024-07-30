<?php

namespace Mlkali\Sa\Support;

class Selector
{
    public function __construct(
        public string $action = '',
        public string $title = '',
        public ?string $article = null,
        public ?string $page = null,
        public ?string $articleID = null,
        private array $queryValues = [],
        private array $url = []
    ) {
        $this->url = isset($_SERVER['REQUEST_URI']) ? explode('/', trim(str_replace(['<', '>', '!', '@', '$'], '', urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))))) : $this->url;
        array_shift($this->url);
        $this->action = $this->url[0] ?? $this->action;
        $this->article = $this->url[1] ?? $this->article;
        $this->page = $this->url[2] ?? $this->page;
        $this->articleID = ($this->article && $this->page) ? $this->article . '|' . $this->page : $this->articleID;
        isset($_SERVER['QUERY_STRING']) ? parse_str($_SERVER['QUERY_STRING'], $this->queryValues) : null;
    }

    //NOTE - maybe some default needed
    public function getQueryMessage(string $search): ?string
    {
        if (array_key_exists($search, $this->queryValues)) {
            return $this->queryValues[$search];
        }
        return null;
    }
}
