<?php

namespace Mlkali\Sa\Database\Entity;

class Member
{

    public function __construct(
        private bool $logged = false,
        private bool $visible = false,
        private string $username = 'visitor',
        protected ?string $password = null,
        private ?string $email = null,
        private ?string $activeMember = null,
        private string $permission = 'visit',
        private ?string $memberName = null,
        private ?string $memberSurname = null,
        private ?string $location = null,
        private $age = null, //not sure about type
        private string $avatar = 'empty_profile.png',
        private ?string $resetToken = null,
        private bool $resetComplete = false,
        private int $bookmarkCount = 0,
        private string $bookmarks = '{}',
        private ?string $memberID = 'visitor|visitor@gmail.com'
    ) {
        if (isset($_SESSION['member_id'])) {
            $this->logged = true;

            foreach ($_SESSION as $key => $value) {
                $this->{$key} = $value;
            }
        }
    }

    public function __set(string $name, $value)
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }

    public function __get(string $name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
    }
}
