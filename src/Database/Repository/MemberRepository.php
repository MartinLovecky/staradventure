<?php

namespace Mlkali\Sa\Database\Repository;

use Mlkali\Sa\Database\DB;

class MemberRepository extends DB{

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

    public function insertIntoMember($request): array
    {
        $token = md5(uniqid(rand(), true));

        $values = [
            'username' => $request->username, 
            'email' => $request->email,
            'password' => password_hash($request->password, PASSWORD_BCRYPT),
            'active' => $token,
            'permission' => 'user',
            'member_id' => $request->username.'|'.$request->email
        ];

        $this->db->query
            ->insertInto('members')
            ->values($values)
            ->execute();   
        
        $id = $this->db->pdo->lastInsertId();

        return ['token' => $token, 'id' => $id];
    }

    public function insertIntoInfo(string $memberID)
    {
        $this->db->query
            ->insertInto('info')
            ->values(['member' => $memberID])
            ->execute();
    }

    public function updateMember(string $memberID, array $set)
    {
        $this->db->query
            ->update('members')
            ->set($set)
            ->where('member_id', $memberID)
            ->execute();
    }

    public function updateInfoMember(string $memberID, array $set)
    {
        $this->db->query
            ->update('info')
            ->set($set)
            ->where('member', $memberID)
            ->execute();
    }
}