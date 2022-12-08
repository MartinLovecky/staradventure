<?php

namespace Mlkali\Sa\Database\Repository;

use Mlkali\Sa\Database\DB;
use Mlkali\Sa\Http\Request;
use Mlkali\Sa\Support\Mailer;

class MemberRepository
{

    public function insertIntoInfo(string $memberID): void
    {
        $this->db->query
            ->insertInto('info')
            ->values(['member' => $memberID])
            ->execute();
    }

    public function insertIntoMember(Request $request, array $memberData): void
    {
        $values = [
            'username' => $request->username,
            'email' => $request->email,
            'password' => password_hash($request->password, PASSWORD_BCRYPT),
            'active' => $memberData['active'],
            'permission' => 'user',
            'member_id' => $memberData['memberID']
        ];

        $this->db->query
            ->insertInto('members')
            ->values($values)
            ->execute();
    }

    public function sendActivationEmail(array $data): void
    {
        //not ussed for activation but I dont want to rebuild $selector
        $ID = rand();
        
        $info = ['subject' => 'Potvrzení registrace', 'to' => $data['recipient']];
        $body = str_replace(
            ['YourUsername', 'MemberID', 'ACHASH', 'TOKEN', 'URL'],
            [$data['username'], $ID, $data['encryptedID'], $data['active'], $_SERVER['HTTP_ORIGIN']],
            file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/public/template/register.php')
        );

        $mailer = new Mailer();
        $mailer->sender($body, $info);
    }

    public function getMemberInfo(string $memberID, $item = null): bool|array
    {
        $db = new DB;
        $stmt = $db->query
            ->from('members')
            ->leftJoin('info ON members.member_id = info.member')
            ->select('info.*')
            ->where('member', $memberID);

        $result =  $stmt->fetch($item);

        if(!$result){
            return 'visitor|123456789';
        }
        return $result;
    }
//STUB - This whole section works but in current "scope" of project dont make sence
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

    public function isUnique(string $username, ?string $email = null): bool
    {
        $idByUsername = $this->getMemberID($username);
        $idByEmail =  $email ?? $this->getMemberID($email); 

        // this two IF blocks are here only because I want use ANY order (email, username <-> username,email, email <-> username)   
        if(filter_var($idByUsername, FILTER_VALIDATE_EMAIL)){
            $whereOne = $idByUsername && $idByEmail ? ['email' => $username, 'username' => $email] : null;
            $whereTwo = isset($idByUsername) && !$idByEmail ? ['email' => $username] : null;
            $whereThree = !$idByUsername && isset($idByEmail) ? ['email' => $email] : null;
            if(isset($whereOne))$memberID = $idByUsername;
       
        }elseif(!filter_var($idByUsername, FILTER_VALIDATE_EMAIL)){
            $whereOne = $idByUsername && $idByEmail ? ['username' => $username, 'email' => $email] : null;
            $whereTwo = isset($idByUsername) && !$idByEmail ? ['username' => $username] : null;
            if(isset($whereOne))$memberID = $idByEmail;
        }
        //change $where based on $whereOne || $whereTwo || $whereThree
        if(isset($whereOne))$where = $whereOne;
        if(isset($whereTwo))$where = $whereTwo;$memberID = $idByUsername;
        if(isset($whereThree))$where = $whereThree;$memberID = $idByUsername;

        //Get ID from DB
        $stmt = $this->db->query
                ->from('members')
                ->select('member_id')
                ->where($where);
        
        //IF Email or Username or both exist = memberID if not false
        $data = $stmt->fetch('member_id');

       if($data || $memberID)
       {
            return false;    
       }
        return true;
    }

    public function resetCompleted(string $email): bool
    {
        $stmt = $this->db->query
                ->from('members')
                ->select('reset_complete')
                ->where('email', $email);

        $result = $stmt->fetch();

        if($result && $result['reset_complete'] == true)
        {
            return false;
        }
            return true;
    }
//STUB - Should be changed in next update hopefully I jusr need to see if Register works as need
//NOTE - Before I will make more changes [ I dont want "rollback" to old code anymore ]
//REVIEW -  All commented section will be deleted soon
    /*
    public function getUserBy($memberID): self
    {
        $stmt = $this->query
                ->from('members')
                ->where('member_id', $memberID);

        return $this;
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

    public function getMemberInfo(string $memberID, $item = null): bool|array
    {
        $stmt = $this->db->query
            ->from('members')
            ->leftJoin('info ON members.member_id = info.member')
            ->select('info.*')
            ->where('member', $memberID);

        return $stmt->fetch($item);
    }

    public function registerMember($request)
    {
        $this->insertIntoInfo($request->username . '|' . $request->email);
        $data = $this->insertIntoMember($request);

        $memberEncryptedID = $this->enc->encrypt($request->username . '|' . $request->email);

        $info = ['subject' => 'Potvrzení registrace', 'to' => $request->email];
        $body = str_replace(
            ['YourUsername', 'MemberID', 'ACHASH', 'TOKEN', 'URL'],
            [$request->username, $data['id'], $memberEncryptedID, $data['token'], $_SERVER['HTTP_ORIGIN']],
            file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/public/template/register.php')
        );

        $this->mailer->sender($body, $info);
    }

    public function activateMember($memberID, $token)
    {
        $decryptID = $this->enc->decrypt($memberID);

        $memberToken = $this->getMemberInfo($decryptID, 'active');
        $memberTID = $this->getMemberInfo($decryptID, 'member_id');

        if (
            strcmp($token, $memberToken) === 0 &&
            strcmp($decryptID, $memberTID) === 0
        ) {
            $this->updateMember($decryptID, ['active' => 'yes']);
        }
    }
*/
}
