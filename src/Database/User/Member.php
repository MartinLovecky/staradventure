<?php

namespace Mlkali\Sa\Database\User;

use Mlkali\Sa\Database\DB;
use Mlkali\Sa\Http\Request;
use Mlkali\Sa\Http\Response;
use Mlkali\Sa\Support\Encryption;

class Member{

    public function __construct(
        private DB $db,
        private Encryption $enc,
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
        
    /**
     * check if user used remeber me box, if yes then user will be rembered until logged out or cookie expires
     *
     * @return void
     */
    public function recallUser(): void
    {
        if($this->remember){
            
            $username = $_COOKIE['remember'];

            $memberID = $this->getMemberID($username);

            $this->setMemberData($memberID);
        }
    }

    /**
     *  username, email || email, username || alone username || alone email (You can use any order you want )
     * @param string $username must be provided
     * @param null|string $email 
     *
     * @return bool true if username|email (or both) dont exist in DATABASE false otherwise
     */
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

    /**
     * Register must be called only after succefull validation inside Controller
     *
     * @param Request $request
     * @return array maybe not needed
     */
    public function register(Request $request): array
    {
        $hashPassword = password_hash($request->password, PASSWORD_BCRYPT);
        $randToken = md5(uniqid(rand(), true));
        $memberID =  $request->username.'|'.$request->email;

        $values = [
            'username' => $request->username,
            'password' => $hashPassword,
            'email' => $request->email,
            'active' => $randToken,
            'permission' => 'user', 
            'reset_complete' => 0,
            'member_id' => $memberID
        ];

        $this->db->query
            ->insertInto('members')
            ->values($values)
            ->execute();

        $id = $this->db->pdo->lastInsertId();

        $this->insertIntoInfo($memberID);

        return ['token' => $randToken, 'id' => $id];
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

    /**
     * activate Member based on query parameters
     *
     * @param  Selector $selector
     * @return Response 
     */
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

    /**
     * Used to get data for /usertable
     *
     * @return array
     */
    public function getAllMembers(): array
    {
        $stmt = $this->db->query->from('members');
        
        $result = $stmt->fetchAll();

        return $result;
    }
    
    /**
     * Admin can change permission of user /usertable
     *
     * @param  string $permission
     * @param  string $id
     * @return Response
     */
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
    
    /**
     * Admin can delete User data 
     *
     * @param  string $id
     * @return Response
     */
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

    /**
     * This should never fail but if you dont trust me you can use try catch block
     *
     * @param string $memberID
     * @return void
     */
    private function insertIntoInfo(string $memberID): void
    {
        $values = [
            'bookmark_count' => 0,
            'bookmarks' => '{}',
            'visible' => false,
            'avatar' => 'empty_profile.png',
            'member' => $memberID
        ];

        $this->db->query
            ->insertInto('info')
            ->values($values)
            ->execute();
    }

}