<?php

namespace Mlkali\Sa\Controllers;

use Mlkali\Sa\Http\Request;
use Mlkali\Sa\Http\Response;
use Mlkali\Sa\Support\Selector;
use Mlkali\Sa\Support\Encryption;
use Mlkali\Sa\Database\Entity\Member;

class MemberController
{

    public function __construct(
        private Selector $selector,
        private Encryption $enc,
        private Member $member
    ) 
    {
    }

    /**
     * Sets $member and inserts values into DB
     *
     * @param Request $request
     * @return void
     */
    public function register(Request $request): void
    {
        // Create a new Member object and initialize its fields
        $member = new Member();
        $member->username = $request->username;
        $member->email = $request->email;
        $member->password = password_hash($request->password, PASSWORD_BCRYPT);
        $member->activeMember = md5(uniqid(rand(), true));
        $member->permission = 'user';
        $member->avatar = 'empty_profile.png';
        $member->memberID = $request->username . '|' . $request->email;
        // Insert the member into the database
        $member->insertIntoInfo($member->memberID);
        $member->insertIntoMember($member);
        // Send an activation email to the user
        $member->sendActivationEmail(
            [
                'username' => $member->username,
                'encryptedID' => $this->enc->encrypt($member->memberID),
                'active' => $member->active,
                'recipient' => $member->email
            ]
        );
    }

    public function activate(Member $member): Response
    {
        $memberID = $this->enc->decrypt($this->selector->secondQueryValue);
        $token = $this->selector->thirdQueryValue;
        
        $memberDB = $member->getMemberInfo('member_id', $memberID, 'member_id');
        $tokenDB = $member->getMemberInfo('member_id', $memberID, 'active');
     
        if(strcmp($memberID, $memberDB) == 0 && strcmp($token, $tokenDB) == 0)
        {
            $member->activateMember($memberID);

            return new Response('/login?message=', 'success.Aktivace úspšná můžete se přihlásit', '#login');
        }

        return new Response('/register?message=', 'danger.Aktivace účtu se nezdařila kontaktujte podporu', '#register');
    }
    
    /*

    public function logout()
    {
        $this->member->logged = false;
    }

    /*

        
    public function recallUser(): void
    {
        if($this->remember){
            
            $username = $_COOKIE['remember'];

            $memberID = $this->getMemberID($username);

            $this->setMemberData($memberID);
        }
    }
    
    public function setMemberData(string $memberID): void
    {
        foreach($this->getMemberInfo($memberID) as $key => $value) {
            
            $_SESSION[$key] = $value;
        }
    }

    public function saveBookmark(string $article, string $page): void
    {
        //Frist bookmark
        $bookmarkCount = ++$this->bookmarkCount; 
        $this->links[$bookmarkCount] .= "/show/$article/$page";
        $bookmarks = json_encode($this->links);

        $this->bookmarkCount = $bookmarkCount;
        $this->bookmarks = $this->links;

        @$_SESSION = ['bookmark_count' => $bookmarkCount, 'bookmarks' => $bookmarks];

        $set = ['bookmark_count' => $bookmarkCount, 'bookmarks' => $bookmarks];

        $this->db->query
            ->update('info')
            ->set($set)
            ->where('username', $this->username)
            ->execute();
    }

    public function removeBookmark(string $bookmarkID, string $memberID): Response
    {
        // frist edit record inside DB
        $bookmarks =  $this->getBookmarkLinks();
        $bookmarkCount = --$this->bookmarkCount;

        unset($bookmarks[$bookmarkID]);

        $set = ['bookmark_count' => $bookmarkCount, 'bookmarks' => json_encode($bookmarks, JSON_FORCE_OBJECT)];

        $this->db->query
            ->update('info')
            ->set($set)
            ->where('member', $memberID)
            ->execute();

        // now Force reload of the object and Redirect to member page
        $this->setMemberData($memberID);

        return new Response('/member'.'/'.$this->username,'success.Záložka '.$bookmarkID.' smazána');
    }


    public function activateMember($selector): Response
    {
        // x=$fristQueryValue&y=$secondQueryValue&z=$thirdQueryValue
        $userData = explode('-', $this->enc->decrypt($selector->secondQueryValue));

        //get data from DB
        $stmt = $this->db->query
                ->from('members')
                ->where('id', $selector->fristQueryValue);

        $result = $stmt->fetch();
    
        if(hash_equals($result['active'], $selector->thirdQueryValue) &&
           $result['username'] === $userData[0] &&
           $result['email'] === $userData[1])
        {
            $stmt = $this->db->query
                    ->update('members')
                    ->set(['active' => 'yes'])
                    ->where('id', $selector->fristQueryValue)
                    ->execute();
            
            return new Response('/login?message=','success.Aktivace úspěšná','#login');
        }
        
        return new Response('/login?message=','danger.Aktivace se nezdařila kontaktuje Support','#login');
    }


    public function getAllMembers(): array
    {
        $stmt = $this->db->query->from('members');
        
        $result = $stmt->fetchAll();

        return $result;
    }
    

    public function setPermission(string $permission, string $id): Response
    {
        $this->permission = $permission;

        $this->db->query
            ->update('members')
            ->set(['permission' => $permission])
            ->where('id', $id)
            ->execute();
        
        return new Response('usertable');
    }
    

    public function deleteUser(string $id): Response
    {
        $this->db->query
            ->deleteFrom('members')
            ->where('id', $id)
            ->execute();

        return new Response('usertable');
    }

    public function logout(): Response
    {
        $this->remember .= false;
        @$_SESSION = array();
        session_destroy();
        unset($_COOKIE['remember']);
        setcookie('remember', '', time() - 3600, '/'); 
        
        return new Response('/#');
    }

    private function getBookmarkLinks(): array
    {
        if ($this->bookmarkCount == 0) {
            return [];
        }

        $stmt = $this->db->query
                ->from('info')
                ->select('bookmarks')
                ->where('member', $this->memberID);
        
        $result = $stmt->fetch('bookmarks');

        if($result){

            return json_decode($result,true);
        }
        
        return [];
    }

   

    private function getMemberInfo(string $memberID): bool|array
    {
        $stmt = $this->db->query
                ->from('members')
                ->leftJoin('info ON members.member_id = info.member')
                ->select('info.*')
                ->where('member', $memberID);
        
        return $stmt->fetch();
    }


    private function insertIntoInfo(string $memberID): void
    {
        $values = [
            'bookmark_count' => 0,
            'bookmarks' => '{}',
            'visible' => false,
            'avatar' => 'empty_profile.png',
            'member' => $memberID
        ];

        try {
            $e = $this->db->query
                ->insertInto('info')
                ->values($values)
                ->execute();

        } catch (Exception $e) {
            throw $e;
        }

        
    }
    */
}
