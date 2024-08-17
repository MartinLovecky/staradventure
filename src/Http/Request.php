<?php

namespace Mlkali\Sa\Http;

class Request
{
    public function __construct(public array $data = [])
    {
        if (!empty($_FILES)) {
            foreach ($_FILES as $fkey => $fvlaue) {
                $this->data[$fkey] = $fvlaue;
            }
        }

        foreach ($this->filter($_POST) as $key => $value) {
            $this->data[$key] = trim($value);
        }
    }

    public function __get(string $key): mixed
    {
        return $this->data[$key];
    }

    public function __set(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * Sanitizes POST data based on expected input types.
     *
     * @param array $data The raw $_POST data
     *
     * @return array The sanitized data
     */
    private function filter(array $data): array
    {
        $filters = [
            'username' => [
                'filter' => FILTER_CALLBACK,
                'options' => function ($value) {
                    return preg_replace('/[^a-zA-Z0-9_]/', '', $value);
                }
            ],
            'email' => FILTER_SANITIZE_EMAIL,
            'url' => FILTER_SANITIZE_URL,
            'name' => [
                'filter' => FILTER_CALLBACK,
                'options' => function ($value) {
                    return preg_replace('/[^a-zA-Z\s-]/', '', $value);
                }
            ],
            // Default fallback for other fields
            'default' => FILTER_SANITIZE_SPECIAL_CHARS,
        ];

        return filter_var_array($data, $filters);
    }
}
