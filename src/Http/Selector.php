<?php

declare(strict_types=1);

namespace Mlkali\Sa\Http;

class Selector
{
    public string $action = '';
    public string $title = '';
    public ?string $article = null;
    public ?string $page = null;
    private array $queryValues = [];
    private array $url = [];

    public function __construct()
    {
        $this->url = $this->parseUrl();
        $this->action = $this->url[0] ?? $this->action;
        $this->article = $this->url[1] ?? $this->article;
        $this->page = $this->url[2] ?? $this->page;
        $this->queryValues = $this->parseQueryString();
    }

    /**
     * Retrieves a message from the query string by key.
     *
     * @param string $search The key to search for in the query parameters.
     *
     * @return string|null The value associated with the key, or null if not found.
     */
    public function getQueryMessage(string $search): ?string
    {
        return $this->queryValues[$search] ?? null;
    }

    /**
     * Parses the current URL and sanitizes it.
     *
     * @return array The parsed and sanitized URL segments.
     */
    private function parseUrl(): array
    {
        if (!isset($_SERVER['REQUEST_URI'])) {
            return [];
        }

        // Decode and sanitize URL path
        $path = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        $sanitizedPath = str_replace(['<', '>', '!', '@', '$'], '', $path);
        $sanitizedPath = filter_var($sanitizedPath, FILTER_SANITIZE_URL);
        // by trim we dont need use array_shift anymore
        return explode('/', trim($sanitizedPath, '/'));
    }

    /**
     * Parses the query string and sanitizes it.
     *
     * @return array The parsed query parameters.
     */
    private function parseQueryString(): array
    {
        $queryValues = [];
        $queryString = $_SERVER['QUERY_STRING'] ?? '';

        if ($queryString) {
            parse_str($queryString, $queryValues);

            foreach ($queryValues as $key => $value) {
                // Sanitize the key
                $sanitizedKey = htmlspecialchars($key, ENT_QUOTES, 'UTF-8');

                // Sanitize the value based on expected data type
                if (is_array($value)) {
                    $sanitizedValue = array_map(function ($item) {
                        return htmlspecialchars($item, ENT_QUOTES, 'UTF-8');
                    }, $value);
                } else {
                    $sanitizedValue = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                }

                // Assign the sanitized value back to the array
                $queryValues[$sanitizedKey] = $sanitizedValue;
            }
        }

        return $queryValues;
    }
}
