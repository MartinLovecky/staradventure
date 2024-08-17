<?php

namespace Mlkali\Sa\Controllers;

use Mlkali\Sa\Http\Request;
use Mlkali\Sa\Http\Response;
use Mlkali\Sa\Support\Messages;
use Mlkali\Sa\Support\Validator;
use Mlkali\Sa\Database\Entity\Member;
use Mlkali\Sa\Support\MessageFormatter;

class MemberController
{
    public function __construct(
        public Member $member,
        public Validator $validator,
        protected MessageFormatter $messageFormatter,
        protected string $token = '',
    ) {
        $this->token = $this->validator->encryption->token();
    }

    public function response(string $type, array $templateData): Response
    {
        if (isset($templateData['body'], $templateData['subject'], $templateData['to'])) {
            $this->validator->memberRepository->sendEmail($templateData['body'], $templateData['subject'], $templateData['to']);
        }
        $message = $this->getMessageForType($type, $templateData['to']);
        $url = "/{$type}?message=";

        return new Response($url, $message, "#{$type}");
    }

    /**
     * - if validation fail redirect to form
     * - if success return valid data
     *
     * @param Request $request
     *
     * @return array|Response
     */
    public function proccesRegister(Request $request): array|Response
    {
        $validate = $this->validator->validateRegister($request);
        // When validation fail
        if ($validate) {
            @$_SESSION = [
                'old_username' => $request->username,
                'old_email' => $request->email
            ];
            return new Response('/register?message=', $validate, '#register');
        }
        $memberRepository = $this->validator->memberRepository;
        // We have valid data
        $memberID = $request->username . '|' . $request->email;
        // info is table for profile edit
        $memberRepository->insert('info', ['member' => $memberID]);
        // insert user data to table
        $memberRepository->insert(
            'members',
            [
                'username' => $request->username,
                'email' => $request->email,
                'password' => password_hash($request->password, PASSWORD_BCRYPT),
                'active' => $this->token,
                'permission' => 'user',
                'member_id' => $memberID
            ]
        );

        $memberID = $this->validator->encryption->encrypt($memberID);
        return [
            'username' => $request->username,
            'encryptedID' => $memberID,
            'token' => $this->token,
            'recipient' => $request->email,
            'memberID' => $memberID
        ];
    }

    public function activate(?string $id, ?string $token): Response
    {
        $memberRepository = $this->validator->memberRepository;

        if (!$id || !$token) {
            return new Response('/index?message=', Messages::INVALID_URL);
        }

        $memberID = $this->validator->encryption->decrypt($id);
        $memberDB = $memberRepository->getMemberInfo('member_id', $memberID, 'member_id');
        $tokenDB = $memberRepository->getMemberInfo('member_id', $memberID, 'active');

        if (strcmp($memberID, $memberDB) == 0 && strcmp($token, $tokenDB) == 0) {
            $this->member->active = 'yes';
            $memberRepository->updateMembersTable($this->member);

            return new Response('/login?message=', Messages::REQUEST_ACTIVATE, '#login');
        }

        return new Response('/register?message=', Messages::REQUEST_ACTIVATE_FAIL, '#register');
    }

    public function proccesLogin(Request $request): Response
    {
        $memberRepository = $this->validator->memberRepository;
        $active = $memberRepository->getMemberInfo('username', $request->username, 'active');
        $activeMember = is_string($active) ? $active : '';
        $validate = $this->validator->validateLogin($request, $activeMember);

        if (isset($validate)) {
            @$_SESSION = ['old_username' => $request->username];

            return new Response('/login?message=', $validate, '#login');
        }

        $this->setMember($request);

        return new Response(
            "member/{$request->username}?message=",
            sprintf(Messages::REQUEST_LOGIN, $request->username),
            '#member'
        );
    }

    public function setMember(Request $request): void
    {
        $memberRepository = $this->validator->memberRepository;
        $memberData = $memberRepository->getMemberInfo('username', $request->username);

        if (isset($request->remember)) {
            $id = $this->validator->encryption->encrypt($_SERVER['REMOTE_ADDR']);
            $username = $this->validator->encryption->encrypt($request->username);
            $userID = $username . '|' . $id;
            setcookie('remember', $userID, time() + (86400 * 7), '/');
        }

        $this->member->logged = true;
        $_SESSION['member'] = serialize($memberData);
    }

    public function proccessResetToken(Request $request): array|Response
    {
        $memberRepository = $this->validator->memberRepository;
        $validate = $this->validator->validateResetSend($request);

        if (isset($validate)) {
            @$_SESSION = ['old_email' => $request->email];

            return new Response('/?message=', $validate, '#reset');
        }

        $memberID = $memberRepository->getMemberInfo('email', $request->email, 'member_id');
        $this->member->reset_token = $this->token;
        $memberRepository->updateMembersTable($this->member);

        $memberID = $this->validator->encryption->encrypt($memberID);

        $templateData = [
            'username' => $request->email,
            'token' => $this->token,
            'encryptedID' => $memberID,
            'recipient' => $request->email,
        ];

        return $templateData;
    }

    public function proccessForgottenUser(Request $request): array|Response
    {
        $memberRepository = $this->validator->memberRepository;
        $validate = $this->validator->validateResetSend($request);

        if (isset($validate)) {
            return new Response(
                '/reset?message=',
                sprintf(Messages::VALIDATION_FORGOTTEN_USER, $request->email),
                '#reset'
            );
        }
        $username = $memberRepository->getMemberInfo('email', $request->email, 'username');
        $memberID = $this->validator->encryption->encrypt($memberRepository->getMemberInfo('email', $request->email, 'member_id'));

        $templateData = [
            'username' => $username,
            'active' => $this->token,
            'encryptedID' => $memberID,
            'recipient' => $request->email,
        ];

        return $templateData;
    }

    public function setNewPassword(Request $request): Response
    {
        $memberRepository = $this->validator->memberRepository;
        $validate = $this->validator->validatePassword($request);

        if (isset($validate)) {
            return new Response('/?message=', $validate, '#newpassword');
        }

        $this->member->password = password_hash($request->password, PASSWORD_BCRYPT);
        $memberRepository->updateMembersTable($this->member);

        return new Response('/?message=', Messages::REQUEST_RESET_PASSWORD, '#login');
    }

    public function logout(): Response
    {
        @$_SESSION = [];
        session_destroy();
        unset($_COOKIE['remember']);
        setcookie('remember', '', time() - 3600, '/');

        return new Response('/?message=', Messages::REQUEST_LOGOUT);
    }

    public function updateMember(Request $request): Response
    {
        $validate = $this->validator->validateAvatar($request);

        if (isset($validate)) {
            return new Response('/updatemember?message=', $validate, '#updatemember');
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

        // Set $member entity with current data or new data from update form
        $this->member->username = !empty($request->username) ? $request->username : $this->member->username;
        $this->member->email = !empty($request->email) ? $request->email : $this->member->email;
        $this->member->name = !empty($request->name) ? $request->name : $this->member->name;
        $this->member->surname = !empty($request->surname) ? $request->surname : $this->member->surname;
        $this->member->age = !empty($request->age) ? $request->age : $this->member->age;
        $this->member->location = !empty($request->location) ? $request->uselocationrname : $this->member->location;
        $this->member->visible = !empty($request->visible) ? $request->visible : $this->member->visible;
        $this->member->avatar = $uploadName;

        $this->update($this->member);

        return new Response("/member/{$request->username}?message=", 'success_Informace upraveny');
    }

    //TODO - this needs to be re-coded
    // 1st get user from database based on $memberID -> then update it based on $permission
    // public function permission(string $permission, string $memberID): Response
    // {
    //     $memberRepository = $this->validator->memberRepository;
    //     $this->member->permission = $permission;
    //     $this->member
    //     $memberRepository->update(['permission' => $permission], $memberID);

    //     return new Response('/usertable?message=', Messages::REQUEST_PERMISSION);
    // }

    public function delete(string $memberID): Response
    {
        $memberRepository = $this->validator->memberRepository;
        $memberRepository->deleteMember($memberID);

        return new Response('/usertable?message=', Messages::REQUEST_DELETE);
    }

    public function allMembers(): array
    {
        $memberRepository = $this->validator->memberRepository;
        return $memberRepository->getMemberInfo();
    }

    private function update(Member $member): void
    {
        $memberRepository = $this->validator->memberRepository;
        $memberRepository->updateMembersTable($member);
        $memberRepository->updateInfoTable($member);
    }

    private function getMessageForType(string $type, string $replace)
    {
        $messageMap = [
            'login' => Messages::REQUEST_LOGIN,
            'register' => Messages::REQUEST_REGISTER,
            'reset' => Messages::REQUEST_RESET_SEND,
            'user' => Messages::REQUEST_FORGOTTEN_USER,
        ];

        $messageTemplate = $messageMap[$type] ?? 'Unknown type: ' . $type;

        return sprintf($messageTemplate, $replace);
    }
}
