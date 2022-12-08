<?php

namespace Mlkali\Sa\Controllers;

use Mlkali\Sa\Http\Request;
use Mlkali\Sa\Database\Entity\Member;
use Mlkali\Sa\Support\Encryption;
use Mlkali\Sa\Support\Selector;

class MemberController
{

    public function __construct(
        private Selector $selector,
        private Encryption $enc
    ) 
    {
    }

    public function register(Request $request)
    {
        $member = new Member();
        $member->username = $request->username;
        $member->memberEmail = $request->email;
        $member->activeMember = md5(uniqid(rand(), true));
        $member->permission = 'user';
        $member->avatar = 'empty_profile.png';
        $member->memberID = $request->username . '|' . $request->email;
        // Insret Into Database
        $member->insertIntoInfo($member->__get('memberID'));
        $this->member->insertIntoMember($request,
            [
                'memberID' => $member->__get('memberID'),
                'active' => $member->__get('active')
            ]
        );
        $encryptedID = $this->enc->encrypt($member->__get('memberID'));
        // Send email to user
        $this->member->sendActivationEmail(
            [
                'username' => $request->username,
                'encryptedID' => $encryptedID,
                'active' => $member->__get('active'),
                'recipient' => $member->__get('memberEmail'),
            ]
        );
    }

    public function activate()
    {
        $this->member->activeMember = 'yes';

        $memberID = $this->selector?->secondQueryValues;
        $token = $this->selector?->thirdQueryValue;

        $this->member->activateMember($memberID, $token);
    }

    public function login(Request $request)
    {
        $this->member->logged = true;

        $this->member->getMemberID($request->username);
    }

    public function logout()
    {
        $this->member->logged = false;
    }

    

    /*
    public function __construct(
        private DB $db,
        private Encryption $enc,
        private Member $member,
        public bool $logged = false,
        public bool $remember = false,
        public bool $visible = false,
        public string $username = 'visitor',
        public ?string $memberID = null,
        public ?string $memberEmail = null,
        public ?string $activeMember = null,
        public string $permission = 'visit',
        public ?string $memberName = null,
        public ?string $memberSurname = null,
        public string $avatar = 'empty_profile.png',
        public ?int $age = null,
        public ?string $location = null,
        public ?string $resetToken = null,
        public bool $resetComplete = false,
        public int $bookmarkCount = 0,
        public array $bookmarks = [],
        private array $links = []
    )
    {
        $this->logged = isset($_SESSION['id']) ? true : $this->logged;
        $this->remember = isset($_COOKIE['remember']) ? true : $this->remember;
        $this->visible = $_SESSION['visible'] ?? $this->visible;
        $this->username = $_SESSION['username'] ?? $this->username;
        $this->memberID = $_SESSION['member_id'] ?? $this->memberID;
        $this->memberEmail = $_SESSION['email'] ?? $this->memberEmail;
        $this->activeMember = $_SESSION['active'] ?? $this->activeMember;
        $this->permission = $_SESSION['permission'] ?? $this->permission;
        $this->memberName = $_SESSION['member_name'] ?? $this->memberName;
        $this->memberSurname = $_SESSION['member_surname'] ?? $this->memberSurname;
        $this->avatar = $_SESSION['avatar'] ?? $this->avatar;
        $this->location = $_SESSION['location'] ?? $this->location;
        $this->bookmarkCount = $_SESSION['bookmark_count'] ?? $this->bookmarkCount;
        $this->bookmarks = isset($_SESSION['bookmarks']) ? json_decode($_SESSION['bookmarks'], true) : $this->bookmarks;
        $this->links = $this->getBookmarkLinks();
    }
        
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
