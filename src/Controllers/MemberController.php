<?php

namespace Mlkali\Sa\Controllers;

use Mlkali\Sa\Http\Request;
use Mlkali\Sa\Http\Response;
use Mlkali\Sa\Support\Messages;
use Mlkali\Sa\Support\Selector;
use Mlkali\Sa\Support\Encryption;
use Mlkali\Sa\Database\Entity\Member;
use Mlkali\Sa\Database\Repository\MemberRepository;
use Mlkali\Sa\Support\Validator;

class MemberController
{

    public function __construct(
        private Selector $selector,
        private Encryption $enc,
        private Member $member,
        private MemberRepository $memRepo,
        private Validator $validator,
        private string $token = ''
    ) {
        $this->token = md5(uniqid(rand(), true));
    }
    public function register(Request $request): Response
    {
        $validate = $this->validator->validateRegister($request);

        if (isset($validate)) {

            @$_SESSION = [
                'old_username' => $request->username,
                'old_email' => $request->email
            ];

            return new Response('/register?message=', $validate, '#register');
        }

        $memberID = $request->username . '|' . $request->email;
        // Insert the member into the database
        $this->memRepo->insert('info', ['member' => $memberID]);
        $this->memRepo->insert('members',
            [
                'username' => $request->username,
                'email' => $request->email,
                'password' => password_hash($request->password, PASSWORD_BCRYPT),
                'active' => $this->token,
                'permission' => 'user',
                'member_id' => $memberID
            ]
        );
        // Send an activation email to the user
        $this->memRepo->sendEmail(
            [
                'username' => $request->username,
                'encryptedID' => $this->enc->encrypt($memberID),
                'active' => $this->token,
                'recipient' => $request->email,
                'templateType' => 'register' 
            ]
        );

        return new Response('/login?message=', sprintf(Messages::REQUETS_REGISTER, $request->email), '#login');
    }

    public function sendResetToken(Request $request): Response
    {
        $validate = $this->validator->validateResetSend($request);

        if (isset($validate)) {
            @$_SESSION = ['old_email' => $request->email];

            return new Response('/?message=', $validate, '#reset');
        }

        $memberID = $this->memRepo->getMemberInfo('email', $request->email, 'member_id');

        $this->memRepo->resetToken($memberID, $this->token);

        $this->memRepo->sendEmail(
            [
                'username' => $request->email,
                'active' => $this->token,
                'encryptedID' => $this->enc->encrypt($memberID),
                'recipient' => $request->email,
                'templateType' => 'reset'
            ]
        );

        return new Response('/?message=', sprintf(Messages::REQUETS_RESET_SEND, $request->email), '#');
    }

    public function sendForgottenUser(Request $request): Response
    {
        $validate = $this->validator->validateResetSend($request);

        if (isset($validate)) {
            return new Response('/reset?message=', sprintf(Messages::VALIDATION_FORGOTEN_USER, $request->email), '#reset');
        }

        $this->memRepo->sendEmail(
            [
                'username' => $this->memRepo->getMemberInfo('email', $request->email, 'username'),
                'active' => $this->token,
                'encryptedID' => $this->enc->encrypt($this->memRepo->getMemberInfo('email', $request->email, 'member_id')),
                'recipient' => $request->email,
                'templateType' => 'user'
            ]
        );

        return new Response('/login?message=', sprintf(Messages::REQUETS_FORGOTEN_USER, $request->email), '#login');
    }

    public function setNewPassword(Request $request): Response
    {
        $validate = $this->validator->validatePassword($request);

        if (isset($validate)) {
            return new Response('/?message=', $validate, '#newpassword');
        }

        $this->memRepo->newPassword($request);

        return new Response('/?message=', Messages::REQUETS_RESET_PASSWORD, '#login');
    }

    public function activate(): Response
    {
        $memberID = $this->enc->decrypt($this->selector->queryID);
        $memberDB = $this->memRepo->getMemberInfo('member_id', $memberID, 'member_id');
        $tokenDB = $this->memRepo->getMemberInfo('member_id', $memberID, 'active');

        if (strcmp($memberID, $memberDB) == 0 && strcmp($this->selector->queryToken, $tokenDB) == 0) 
        {
            $this->memRepo->activateMember($memberID);
            return new Response('/login?message=', Messages::REQUEST_ACTIVATE, '#login');
        }

        return new Response('/register?message=', Messages::REQUEST_ACTIVATE_FAIL, '#register');
    }

    public function login(Request $request): Response
    {
        $active = $this->memRepo->getMemberInfo('username', $request->username, 'active');
        $activeMember = is_string($active) ? $active : '';
        $validate = $this->validator->validateLogin($request, $activeMember);

        if (isset($validate)) {
            @$_SESSION = ['old_username' => $request->username];

            return new Response('/login?message=', $validate, '#login');
        }
        elseif (isset($request->remember)) {

            setcookie('remember', $request->username, time() + (86400 * 7), '/');

            return new Response('member/' . $request->username . '?message=', sprintf(Messages::REQUETS_LOGIN, $request->username));
        }

        $this->setMember($request->username);

        return new Response('member/' . $request->username . '?message=', sprintf(Messages::REQUETS_LOGIN, $request->username));
    }

    public function logout(): Response
    {
        @$_SESSION = array();
        session_destroy();
        unset($_COOKIE['remember']);
        setcookie('remember', '', time() - 3600, '/');

        return new Response('/?message=', Messages::REQUEST_LOGOUT, '#');
    }

    //TODO - test
    public function updateMember(Request $request): Response
    {
        return dd("x");
        $validate = $this->validator->validateAvatar($request);

        if (isset($validate)) {
            return new Response('/reset?message=', $validate, '#updatemember');
        }

        $allowedTypes = [
            'image/png' => 'png',
            'image/jpeg' => 'jpeg',
            'image/jpg' => 'jpg'
        ];

        $extension = $allowedTypes[$request->avatar['type']];

        $uploadName = $request->avatar['name'] . '.' . $extension;
        $targetDir = $_SERVER['DOCUMENT_ROOT'] . '/public/img/avatars/';

        $newFilePath = $targetDir . $request->avatar['name'] . '.' . $extension;

        move_uploaded_file($request->avatar['tmp_name'], $newFilePath);
        unlink($request->avatar['tmp_name']);

        $this->member->username = $request->username ?? $this->member->username;
        $this->member->email = $request->email ?? $this->member->email;
        $this->member->name = $request->name ?? $this->member->name;
        $this->member->surname = $request->surname ?? $this->member->surname;
        $this->member->age = $request->age ?? $this->member->age;
        $this->member->location = $request->location ?? $this->member->location;
        $this->member->visible = $request->visible ?? $this->member->visible;
        $this->member->avatar = $uploadName;

        $this->memRepo->updateMember($this->member);
        $this->memRepo->updateInfoMember($this->member);

        return new Response('/member' . $request->username . '?message=', 'succes.Informace upraveny');
    }

    public function permission(string $permission, string $memberID): Response
    {
        $this->memRepo->setPermission($permission, $memberID);

        return new Response('/usertable?message=', Messages::REQUEST_PERMISSION);
    }

    public function delete(string $memberID): Response
    {
        $this->memRepo->deleteMember($memberID);

        return new Response('/usertable?message=', Messages::REQUEST_DELETE);
    }

    public function setMember(string $username): void
    {
        $memberData = $this->memRepo->getMemberInfo('username', $username);

        foreach ($memberData as $key => $value)
            @$_SESSION[$key] = $value;
    }

    public function allMembers(): array
    {
        return $this->memRepo->getMemberInfo();
    }
}
