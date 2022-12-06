<?php

namespace Mlkali\Sa\Database\Entity;

use Mlkali\Sa\Database\Repository\MemberRepository;

class Member extends MemberRepository{

    public function __construct(
        private bool $visible = false,
        private string $username = 'visitor',
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
        private ?string $memberID = null
    )
    {   
    }

    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            $this->name = $this->getMemberInfo($this->memberID, $name);
            return $this->$name;
        }
    }

    public function setMemberID(string $check): ?string
    {
        $this->memberID = $this->getMemberID($check);
        return $this->memberID;
    }

    public function getMemberInfo(string $memberID, $item = null): bool|array
    {
        $stmt = $this->db->query
                ->from('members')
                ->leftJoin('info ON members.member_id = info.member')
                ->select('info.*')
                ->where('member', $memberID);
        
        return $stmt->fetch($item);
    }
}