<?php

namespace Mlkali\Sa\Controllers;

use Mlkali\Sa\Http\Request;
use Mlkali\Sa\Http\Response;
use Mlkali\Sa\Support\Selector;
use Mlkali\Sa\Support\Encryption;
use Mlkali\Sa\Database\Entity\Member;
use Mlkali\Sa\Database\Repository\MemberRepository;

class MemberController
{

    public function __construct(
        private Selector $selector,
        private Encryption $enc,
        private Member $member,
        private MemberRepository $memRepo
    ) {
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
        $this->memRepo->insertIntoInfo($member->memberID);
        $this->memRepo->insertIntoMember($member);
        // Send an activation email to the user
        $this->memRepo->sendActivationEmail(
            [
                'username' => $member->username,
                'encryptedID' => $this->enc->encrypt($member->memberID),
                'active' => $member->active,
                'recipient' => $member->email
            ]
        );
    }

    public function activate(): Response
    {
        $memberID = $this->enc->decrypt($this->selector->secondQueryValue);
        $token = $this->selector->thirdQueryValue;

        $memberDB = $this->memRepo->getMemberInfo('member_id', $memberID, 'member_id');
        $tokenDB = $this->memRepo->getMemberInfo('member_id', $memberID, 'active');

        if (strcmp($memberID, $memberDB) == 0 && strcmp($token, $tokenDB) == 0) {
            $this->memRepo->activateMember($memberID);

            return new Response('/login?message=', 'success.Aktivace úspšná můžete se přihlásit', '#login');
        }

        return new Response('/register?message=', 'danger.Aktivace účtu se nezdařila kontaktujte podporu', '#register');
    }

    public function login(string $username): void
    {
        $memberData = $this->memRepo->getMemberInfo('username', $username);

        foreach ($memberData as $key => $value) {
            @$_SESSION[$key] = $value;
        }
    }

    public function logout(): Response
    {
        @$_SESSION = array();
        session_destroy();
        unset($_COOKIE['remember']);
        setcookie('remember', '', time() - 3600, '/');

        return new Response('/#');
    }

    public function update(Request $request, string $avatar): void
    {
        $this->member->username = $request->username ?? $this->member->username;
        $this->member->email = $request->email ?? $this->member->email;
        $this->member->name = $request->name ?? $this->member->name;
        $this->member->surname = $request->surname ?? $this->member->surname;
        $this->member->age = $request->age ?? $this->member->age;
        $this->member->location = $request->location ?? $this->member->location;
        $this->member->visible = $request->visible ?? $this->member->visible;

        $this->member->avatar = $avatar;

        $this->memRepo->updateMember($this->member);
        $this->memRepo->updateInfoMember($this->member);
    }

    public function permission(string $permission, string $memberID): Response
    {
        $this->memRepo->setPermission($permission, $memberID);

        return new Response('/usertable');
    }

    public function delete(string $memberID): Response
    {
        $this->memRepo->deleteMember($memberID);

        return new Response('/usertable');
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
*/
}
