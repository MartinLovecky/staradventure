<?php

namespace Mlkali\Sa\Database\Repository;

use Mlkali\Sa\Database\DB;
use Mlkali\Sa\Support\Mailer;


class MemberRepository{

    public function __construct(
        private DB $db,
        private Mailer $mailer
    )
    {
    }
    
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
        $db = new DB();
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

    public function getAllMembers(): array
    {
        $db = new DB();
        $stmt = $db->query->from('members');
        
        $result = $stmt->fetchAll();

        return $result;
    }

    public function insertIntoInfo(string $memberID): void
    {   
        $db = new DB();
        $db->query->insertInto('info')->values(['member' => $memberID])->execute();
    }

    public function insertIntoMember($member): void
    {
        $values = [
            'username' => $member->username,
            'email' => $member->email,
            'password' => $member->password,
            'active' => $member->activeMember,
            'permission' => $member->permission,
            'member_id' => $member->memberID
        ];
        $db = new DB();
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

        $this->mailer->sender($body, $info);
    }

    public function activateMember(string $memberID): void
    {   
        $db = new DB();
        $db->query->update('members')->set(['active' => 'yes'])->where('member_id', $memberID)->execute();
    }

    public function sendResetToken(array $data): void
    {
        $db = new DB();
        $db->query
            ->update('members')
            ->set(['reset_token' => $data['token']])
            ->where('member_id', $data['memberID'])
        ->execute();

        $info = ['subject '=> 'Reset hesla','to'=> $data['email']];
        $body = str_replace(
            ['YourUsername', 'TOKEN', 'URL', 'ACHASH'], 
            [$data['email'], $data['token'], $_SERVER['SERVER_NAME'], $data['id']], 
            file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/public/template/reset.php')
        );

        $this->mailer->sender($body, $info);
    }
    
/*
//STUB - This whole section works but in current "scope" of project dont make sence
    
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
   

   
//STUB - Should be changed in next update hopefully I jusr need to see if Register works as need
//NOTE - Before I will make more changes [ I dont want "rollback" to old code anymore ]
//REVIEW -  All commented section will be deleted soon
*/
}
