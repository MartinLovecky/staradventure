<?php

namespace Mlkali\Sa\Controllers;

use Mlkali\Sa\Http\Request;
use Mlkali\Sa\Http\Response;
use Mlkali\Sa\Support\Messages;
use Mlkali\Sa\Support\Validator;
use Mlkali\Sa\Database\Entity\Member;

class MemberController
{
    public function __construct(
        protected Member $member,
        protected Validator $validator,
        protected string $token = '',
    ) {
        $this->token = $this->validator->memberRepository->messages->encryption->token();
    }

    public function register(Request $request): Response
    {
        $memberRepository = $this->validator->memberRepository;
        $validate = $this->validator->validateRegister($request);

        if (isset($validate)) {

            @$_SESSION = [
                'old_username' => $request?->username,
                'old_email' => $request?->email
            ];

            return new Response('/register?message=', $validate, '#register');
        }

        $memberID = $request?->username . '|' . $request?->email;
        // Insert the member into the database
        $memberRepository->insert('info', ['member' => $memberID]);
        $memberRepository->insert(
            'members',
            [
                'username' => $request?->username,
                'email' => $request?->email,
                'password' => password_hash($request?->password, PASSWORD_BCRYPT),
                'active' => $this->token,
                'permission' => 'user',
                'member_id' => $memberID
            ]
        );
        // Send an activation email to the user
        $memberRepository->sendEmail(
            [
                'username' => $request?->username,
                'encryptedID' => $memberRepository->messages->encryption->encrypt($memberID),
                'active' => $this->token,
                'recipient' => $request?->email,
                'templateType' => 'register'
            ]
        );

        return new Response(
            '/login?message=',
            sprintf(Messages::REQUETS_REGISTER, $request->email),
            '#login'
        );
    }

    public function sendResetToken(Request $request): Response
    {
        $memberRepository = $this->validator->memberRepository;
        $validate = $this->validator->validateResetSend($request);

        if (isset($validate)) {
            @$_SESSION = ['old_email' => $request?->email];

            return new Response('/?message=', $validate, '#reset');
        }

        $memberID = $memberRepository->getMemberInfo('email', $request?->email, 'member_id');

        $memberRepository->update(['reset_token' => $this->token], $memberID);

        $memberRepository->sendEmail(
            [
                'username' => $request->email,
                'active' => $this->token,
                'encryptedID' => $memberRepository->messages->encryption->encrypt($memberID),
                'recipient' => $request->email,
                'templateType' => 'reset'
            ]
        );

        return new Response(
            '/?message=',
            sprintf(Messages::REQUETS_RESET_SEND, $request->email),
            '#'
        );
    }

    public function sendForgottenUser(Request $request): Response
    {
        $memberRepository = $this->validator->memberRepository;
        $validate = $this->validator->validateResetSend($request);

        if (isset($validate)) {
            return new Response(
                '/reset?message=',
                sprintf(Messages::VALIDATION_FORGOTEN_USER, $request->email),
                '#reset'
            );
        }

        $memberRepository->sendEmail(
            [
                'username' => $memberRepository->getMemberInfo('email', $request->email, 'username'),
                'active' => $this->token,
                'encryptedID' => $memberRepository->messages->encryption->encrypt($memberRepository->getMemberInfo('email', $request->email, 'member_id')),
                'recipient' => $request->email,
                'templateType' => 'user'
            ]
        );

        return new Response(
            '/login?message=',
            sprintf(Messages::REQUETS_FORGOTEN_USER, $request->email),
            '#login'
        );
    }

    public function setNewPassword(Request $request): Response
    {
        $memberRepository = $this->validator->memberRepository;
        $validate = $this->validator->validatePassword($request);

        if (isset($validate)) {
            return new Response('/?message=', $validate, '#newpassword');
        }

        $set = ['password' => password_hash($request->password, PASSWORD_BCRYPT)];
        $memberRepository->update($set, $request->user_id);

        return new Response('/?message=', Messages::REQUETS_RESET_PASSWORD, '#login');
    }

    public function activate(): Response
    {
        $memberRepository = $this->validator->memberRepository;
        $messages = $memberRepository->messages;
        $selector = $messages->selector;

        // FIXME can be null
        $id = $selector->getQueryMessage("id");
        $token = $selector->getQueryMessage("token");

        $memberID = $messages->encryption->decrypt($id);
        $memberDB = $memberRepository->getMemberInfo('member_id', $memberID, 'member_id');
        $tokenDB = $memberRepository->getMemberInfo('member_id', $memberID, 'active');

        if (strcmp($memberID, $memberDB) == 0 && strcmp($token, $tokenDB) == 0) {
            $memberRepository->update(['active' => 'yes'], $memberID);
            return new Response(
                '/login?message=',
                Messages::REQUEST_ACTIVATE,
                '#login'
            );
        }

        return new Response(
            '/register?message=',
            Messages::REQUEST_ACTIVATE_FAIL,
            '#register'
        );
    }

    public function login(Request $request): Response
    {
        $memberRepository = $this->validator->memberRepository;
        $active = $memberRepository->getMemberInfo('username', $request->username, 'active');
        $activeMember = is_string($active) ? $active : '';
        $validate = $this->validator->validateLogin($request, $activeMember);

        if (isset($validate)) {
            @$_SESSION = ['old_username' => $request->username];

            return new Response('/login?message=', $validate, '#login');
        } elseif (isset($request->remember)) {

            setcookie('remember', $request->username, time() + (86400 * 7), '/');

            return new Response(
                "member/{$request->username}?message=",
                sprintf(Messages::REQUETS_LOGIN, $request->username)
            );
        }

        $this->setMember($request->username);

        return new Response(
            "member/{$request->username}?message=",
            sprintf(Messages::REQUETS_LOGIN, $request->username)
        );
    }

    public function logout(): Response
    {
        @$_SESSION = array();
        session_destroy();
        unset($_COOKIE['remember']);
        setcookie('remember', '', time() - 3600, '/');

        return new Response('/?message=', Messages::REQUEST_LOGOUT, '#');
    }

    public function updateMember(Request $request): Response
    {

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

        $this->update($this->member);

        return new Response('/member' . $request->username . '?message=', 'succes.Informace upraveny');
    }

    public function permission(string $permission, string $memberID): Response
    {
        $memberRepository = $this->validator->memberRepository;
        $memberRepository->update(['permission' => $permission], $memberID);

        return new Response('/usertable?message=', Messages::REQUEST_PERMISSION);
    }

    public function delete(string $memberID): Response
    {
        $memberRepository = $this->validator->memberRepository;
        $memberRepository->deleteMember($memberID);

        return new Response('/usertable?message=', Messages::REQUEST_DELETE);
    }

    public function setMember(string $username): void
    {
        $memberRepository = $this->validator->memberRepository;
        $memberData = $memberRepository->getMemberInfo('username', $username);

        foreach ($memberData as $key => $value) {
            @$_SESSION[$key] = $value;
        }
    }

    public function allMembers(): array
    {
        $memberRepository = $this->validator->memberRepository;
        return $memberRepository->getMemberInfo();
    }

    private function update(Member $member): void
    {
        $memberRepository = $this->validator->memberRepository;
        $memberRepository->update(
            [
                'username' => $member->username,
                'email' => $member->email,
                'avatar' => $member->avatar
            ],
            $member->memberID
        );

        $memberRepository->updateInfoMember($member);
    }
}
