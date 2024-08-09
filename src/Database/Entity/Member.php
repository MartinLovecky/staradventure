<?php

namespace Mlkali\Sa\Database\Entity;

class Member
{
    public function __construct(public array $data = [])
    {
        if (isset($_SESSION['member_id'])) {
            $this->data['logged'] = true;
        } else {
            $this->data['logged'] = false;
        }
    }

    public function __get(string $name): mixed
    {
        return $this->data[$name];
    }

    public function __set(string $name, $value): void
    {
        $this->data[$name] = $value;
    }
}
