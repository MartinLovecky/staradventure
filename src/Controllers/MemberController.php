<?php

namespace Mlkali\Sa\Controllers;

use Exception;
use Mlkali\Sa\Http\Request;
use Mlkali\Sa\Http\Response;
use Mlkali\Sa\Support\Messages;
use Mlkali\Sa\Support\Validator;
use Mlkali\Sa\Database\Entity\Member;
use Mlkali\Sa\Support\Selector;

class MemberController
{
    public function __construct(
        public Member $member,
        public Validator $validator,
        protected string $token = '',
        protected string $url = ''
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
        $templateData = [
            'username' => $request->username,
            'encryptedID' => $memberID,
            'token' => $this->token,
            'recipient' => $request->email,
            'memberID' => $memberID
        ];

        return $templateData;
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
        } elseif (isset($request->remember)) {

            setcookie('remember', $request->username, time() + (86400 * 7), '/');

            return new Response(
                "member/{$request->username}?message=",
                sprintf(Messages::REQUEST_LOGIN, $request->username),
                '#member'
            );
        }

        $this->setMember($request->username);

        return new Response(
            "member/{$request->username}?message=",
            sprintf(Messages::REQUEST_LOGIN, $request->username),
            '#member'
        );
    }

    public function setMember(string $username): void
    {
        $memberRepository = $this->validator->memberRepository;
        $memberData = $memberRepository->getMemberInfo('username', $username);

        $_SESSION['member_id'] = $memberData['member_id'];

        foreach ($memberData as $key => $value) {
            $this->member->{$key} = $value;
        }
    }

    public function proccessResetToken(Request $request): array|Response
    {
        $memberRepository = $this->validator->memberRepository;
        $validate = $this->validator->validateResetSend($request);

        if (isset($validate)) {
            @$_SESSION = ['old_email' => $request?->email];

            return new Response('/?message=', $validate, '#reset');
        }

        $memberID = $memberRepository->getMemberInfo('email', $request?->email, 'member_id');

        $memberRepository->update(['reset_token' => $this->token], $memberID);

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

        $set = ['password' => password_hash($request->password, PASSWORD_BCRYPT)];
        $memberRepository->update($set, $request->user_id);

        return new Response('/?message=', Messages::REQUEST_RESET_PASSWORD, '#login');
    }

    public function activate(Selector $selector): Response
    {
        $memberRepository = $this->validator->memberRepository;
        $messages = $memberRepository->messages;

        $id = $selector->getQueryMessage("id");
        $token = $selector->getQueryMessage("token");

        if (!$id || !$token) {
            return new Response('/index?message=', Messages::INVALID_URL);
        }

        $memberID = $this->validator->encryption->decrypt($id);
        $memberDB = $memberRepository->getMemberInfo('member_id', $memberID, 'member_id');
        $tokenDB = $memberRepository->getMemberInfo('member_id', $memberID, 'active');

        if (strcmp($memberID, $memberDB) == 0 && strcmp($token, $tokenDB) == 0) {
            $memberRepository->update(['active' => 'yes'], $memberID);

            return new Response('/login?message=', Messages::REQUEST_ACTIVATE, '#login');
        }

        return new Response('/register?message=', Messages::REQUEST_ACTIVATE_FAIL, '#register');
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

        return new Response("/member/{$request->username}?message=", 'succes.Informace upraveny');
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
