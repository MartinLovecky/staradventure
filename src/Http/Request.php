<?php

namespace Mlkali\Sa\Http;

class Request
{
    public function __construct(public array $data = [])
    {
        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $value) {
                $this->data[$key] = $value;
            }
        }

        foreach ($_POST as $key => $value) {
            $this->data[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
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
}
