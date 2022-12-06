<?php

namespace Mlkali\Sa\Database\Repository;

use Mlkali\Sa\Database\DB;

class MemberRepository extends DB{

    private array $buffer = [];

    public function readyToSet(string $key, $params): self
    {
        $this->buffer[$key] .= $params;
        return $this;
    }

    public function getUserBy($memberID): self
    {
        $stmt = $this->query
                ->from('members')
                ->where('member_id', $memberID);

        return $this;
    }

    public function getMemberID(?string $check = null): bool|string
    {
        if(!filter_var($check, FILTER_VALIDATE_EMAIL)){
            $stmt = $this->db->query
                    ->from('members')
                    ->select('username')
                    ->where('username', $check);
            
            $result = $stmt->fetch('member_id');

            return $result;
        }

        if(filter_var($check, FILTER_VALIDATE_EMAIL))
        {
            $stmt = $this->db->query
                    ->from('members')
                    ->select('email')
                    ->where('email', $check);
            
            $result = $stmt->fetch('member_id');

            return $result;
        }
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