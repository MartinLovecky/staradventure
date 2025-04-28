<?php

declare(strict_types=1);

namespace Mlkali\Sa\Database\Entity;

use Mlkali\Sa\Database\Repository\MemberRepository;
use Mlkali\Sa\Security\Encryption;

class Member
{
    public array $data = [];

    public function __construct(private MemberRepository $memberRepository)
    {
        $this->getMember();
    }

    public function __get(string $name): mixed
    {
        return $this->data[$name] ?? null;
    }

    public function __set(string $name, $value): void
    {
        $this->data[$name] = $value;
    }

    private function getMember(): void
    {
        if (isset($_SESSION['member'])) {
            $this->logged = true;
            $member = unserialize($_SESSION['member']);
            foreach ($member as $key => $value) {
                $this->{$key} = $value;
            }
        } elseif (!isset($_SESSION['member']) && isset($_COOKIE['remember'])) {
            $this->getUserFromCookieToken();
        } else {
            $this->logged = false;
        }
    }

    private function getUserFromCookieToken(): void
    {
        $encryption = new Encryption();
        $parts = explode('|', $_COOKIE['remember']);
        $username = $encryption->decrypt($parts[0]) ?? null;
        $id = $encryption->decrypt($parts[1]) ?? null;

        if ($id === $_SERVER['REMOTE_ADDR']) {
            $memberData = $this->memberRepository->getMemberInfo('username', $username);
            $memberData['logged'] = true;
            $_SESSION['member'] = serialize($memberData);
        }
    }
}
