<?php

namespace Mlkali\Sa\Database\Entity;

use Mlkali\Sa\Database\Repository\MemberRepository;

class Member extends MemberRepository
{

    public function __construct(
        private bool $logged = false,
        private bool $visible = false,
        private string $username = 'visitor',
        protected ?string $password = null,
        private ?string $memberEmail = null,
        private ?string $activeMember = null,
        private string $permission = 'visit',
        private ?string $memberName = null,
        private ?string $memberSurname = null,
        private string $avatar = 'empty_profile.png',
        private ?string $resetToken = null,
        private bool $resetComplete = false,
        private int $bookmarkCount = 0,
        private array $bookmarks = [],
        private ?string $memberID = 'visitor|123456789'
    )
    {
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