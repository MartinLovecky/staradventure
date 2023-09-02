<?php

namespace Mlkali\Sa\Http;
class Request
{
    public function __construct(public array $data = [])
    {
        if(!empty($_FILES)) {
            foreach($_FILES as $fkey => $fvlaue) {
                $this->data[$fkey] = $fvlaue;
            }
        }

        foreach($_POST as $key => $value) {
            $this->data[$key] = $value;
        }
    }

    public function __get(string $key): mixed
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        return null;
    }

    public function __set(string $key, $value): void
    {
        $this->data[$key] = $value;
    }
}
