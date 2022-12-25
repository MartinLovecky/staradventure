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
        private MemberRepository $memRepo,
        private string $token = ''
    ) {
        $this->token = md5(uniqid(rand(), true));
    }
    /**
     * Registers a new member and sends an activation email
     *
     * @param Request $request The request object containing the registration data
     * @return void
     */
    public function register(Request $request): void
    {
        $memberID = $request->username . '|' . $request->email;

        // Insert the member into the database
        $this->memRepo->insert('members', [
            'username' => $request->username,
            'email' => $request->email,
            'password' => password_hash($request->password, PASSWORD_BCRYPT),
            'active' => $this->token,
            'permission' => 'user',
            'member_id' => $memberID
        ]);
        $this->memRepo->insert('info', ['member' => $memberID]);
        // Send an activation email to the user
        $this->memRepo->sendEmail(
            [
                'username' => $request->username,
                'encryptedID' => $this->enc->encrypt($memberID),
                'active' => $this->token,
                'recipient' => $request->email
            ],
            'register'
        );
    }
    /**
     * Sends a reset token to the member with the specified email
     *
     * @param Request $request The request object containing the email
     * @return void
     */
    public function sendResetToken(Request $request): void
    {
        $memberID = $this->memRepo->getMemberInfo('email', $request->email, 'member_id');

        $this->memRepo->resetToken($memberID, $this->token);

        $this->memRepo->sendEmail(
            [
                'username' => $request->email,
                'active' => $this->token,
                'encryptedID' => $this->enc->encrypt($memberID),
                'recipient' => $request->email
            ],
            'reset'
        );
    }
    /**
     * Sends the forgotten username to the member with the specified email
     *
     * @param Request $request The request object containing the email
     * @return void
     */
    public function sendForgottenUser(Request $request): void
    {
        $username = $this->memRepo->getMemberInfo('email', $request->email, 'username');
        $memberID = $this->memRepo->getMemberInfo('email', $request->email, 'member_id');

        $this->memRepo->sendEmail(
            [
                'username' => $username,
                'active' => $this->token,
                'encryptedID' => $this->enc->encrypt($memberID),
                'recipient' => $request->email
            ],
            'user'
        );
    }
    /**
     * Sets a new password for the member with the specified email
     *
     * @param Request $request The request object containing the password and email
     * @return void
     */
    public function setNewPassword(Request $request): void
    {
        $this->memRepo->newPassword($request);
    }
    /**
     * Gets all members from the database
     *
     * @return array An array of all members
     */
    public function allMembers(): array
    {
        return $this->memRepo->getMemberInfo();
    }
    /**
     * Activates the member with the specified ID and token
     *
     * @return Response A response object with the activation status
     */
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
    /**
     * Logs in the member with the specified username
     *
     * @param string $username The username of the member to log in
     * @return void
     */
    public function login(string $username): void
    {
        $memberData = $this->memRepo->getMemberInfo('username', $username);

        foreach ($memberData as $key => $value) {
            @$_SESSION[$key] = $value;
        }
    }
    /**
     * Logs out the current user and destroys the session
     *
     * @return Response A response object with the logout status
     */
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