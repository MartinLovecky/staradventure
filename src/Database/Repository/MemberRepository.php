<?php

namespace Mlkali\Sa\Database\Repository;

use Mlkali\Sa\Database\DB;
use Mlkali\Sa\Support\Encryption;
use Mlkali\Sa\Support\Mailer;


class MemberRepository{

    public function __construct(
        private DB $db,
        private Mailer $mailer,
        private Encryption $enc
    )
    {}
    
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

    public function sendResetToken($request): void
    {
        $memberID = $this->getMemberInfo('email', $request->email, 'member_id');
        $token = md5(uniqid(rand(), true));

        $db = new DB();
        $db->query
            ->update('members')
            ->set(['reset_token' => $token])
            ->where('member_id', $memberID)
        ->execute();

        $info = ['subject '=> 'Reset hesla','to'=> $request->email];
        $body = str_replace(
            ['YourUsername', 'TOKEN', 'URL', 'ACHASH'], 
            [$request->email, $token, $_SERVER['SERVER_NAME'], $this->enc->encrypt($memberID)], 
            file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/public/template/reset.php')
        );

        $this->mailer->sender($body, $info);
    }

    public function setNewPassword($request): void
    {
        $db = new DB();

        $db->query
            ->update('members')
            ->set(['password' => password_hash($request->password, PASSWORD_BCRYPT)])
            ->where('email', $request->email)
        ->execute();
    }

    public function sendForgottenUser(string $email): void
    {   
        $username = $this->getMemberInfo('email', $email, 'username');
    
        $body = str_replace(
            ['YourUsername', 'URL'], 
            [$username, $_SERVER['SERVER_NAME']], 
            file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/public/template/username.php')
        );
    
        $info = ['subject' => 'Zapomenutné Username', 'to' => $email];
    
        $this->mailer->sender($body, $info);
    }
/*
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
*/
}
