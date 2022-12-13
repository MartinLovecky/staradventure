<?php

namespace Mlkali\Sa\Database\Repository;

use Mlkali\Sa\Database\DB;
use Mlkali\Sa\Support\Mailer;

class MemberRepository{
    
    /**
     * This function gets data from specific member not all members data
     *
     * @param string $column name form DB example: 'member_id' etc..
     * @param string $value  of column we search
     * @param string|null $item
     *  - null and DB row exits $result will be array
     *  - string $result will return specific column value
     *  - false if row does not exist
     * @return mixed — string, array or false if there is no row
     */
    public function getMemberInfo(string $column, string $value = null, ?string $item = null)
    {
        $db = new DB;

        $stmt = $db->query->from('members')
                ->leftJoin('info ON members.member_id = info.member')
                ->select('info.*')
                ->where($column, $value);

        $result =  $stmt->fetch($item);

        if(!$result){
            return 'visitor|vistor@gmail.com';
        }
        return $result;
    }

    public function insertIntoInfo(string $memberID): void
    {
        $db = new DB;
        $db->query->insertInto('info')->values(['member' => $memberID])->execute();
    }

    public function insertIntoMember($member): void
    {
        $db = new DB;
        $values = [
            'username' => $member->username,
            'email' => $member->email,
            'password' => $member->password,
            'active' => $member->activeMember,
            'permission' => $member->permission,
            'member_id' => $member->memberID
        ];

        $db->query->insertInto('members')->values($values)->execute();
    }

    public function sendActivationEmail(array $data): void
    {
        //not ussed for activation but I dont want to rebuild $selector
        $ID = rand();

        $info = ['subject' => 'Potvrzení registrace', 'to' => $data['recipient']];
        $body = str_replace(
            ['YourUsername', 'MemberID', 'ACHASH', 'TOKEN', 'URL'],
            [$data['username'], $ID, $data['encryptedID'], $data['active'], $_SERVER['SERVER_NAME']],
            file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/public/template/register.php')
        );

        $mailer = new Mailer();
        $mailer->sender($body, $info);
    }

    public function activateMember(string $memberID): void
    {
        $db = new DB;
        $db->query->update('members')->set(['active' => 'yes'])->where('member_id', $memberID)->execute();
    }
    
/*
//STUB - This whole section works but in current "scope" of project dont make sence
    

   

    public function resetCompleted(string $email): bool
    {
        $stmt = $this->query
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
        $this->query
            ->update('members')
            ->set($set)
            ->where('member_id', $memberID)
            ->execute();
    }

    public function updateInfoMember(string $memberID, array $set)
    {
        $this->query
            ->update('info')
            ->set($set)
            ->where('member', $memberID)
            ->execute();
    }

    public function getMemberInfo(string $memberID, $item = null): bool|array
    {
        $stmt = $this->query
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

    
*/
}
