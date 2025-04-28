<?php

declare(strict_types=1);

namespace Mlkali\Sa\Controllers;

use Mlkali\Sa\Http\{Mailer, Request, Response, Selector};
use Mlkali\Sa\Security\{Encryption, Validator};
use Mlkali\Sa\Support\Messages;
use Mlkali\Sa\Database\Entity\Member;
use Mlkali\Sa\Database\Repository\MemberRepository;

class MemberController
{
    private string $token = '';

    public function __construct(
        private Encryption $encryption,
        private Selector $selector,
        private MemberRepository $memberRepository,
        private Member $member,
        private Mailer $mailer,
        private Validator $validator,
    ) {
        $this->token = $this->encryption->token();
    }

    public function response(string $type = '', array $data = []): Response
    {
        if (!empty($data)) {
            $this->mailer->sender(
                body:$data['body'],
                subject:$data['subject'],
                to:$data['to']
            );
        }

        $message = $this->getMessageForType($type, $data['to']);
        $url = "/{$type}?d={$data['encryptedID']}&message=";

        return new Response($url, $message, "#{$type}");
    }

    public function register(Request $request): array|Response
    {
        $_SESSION = [
            'old_username' => $request->username,
            'old_email' => $request->email
        ];

        if ($validate = $this->validator->validateRegister(request:$request)) {
            return new Response(
                '/register?message=',
                $validate,
                '#register'
            );
        }

        $memberID = "{$request->username}|{$request->email}";

        if ($this->memberRepository->exist(id:$memberID)) {
            return new Response(
                '/register?message=',
                Messages::VALID_USER_ALREADY_EXISTS,
                '#register'
            );
        }

        $this->memberRepository->insert(table:'info', values:['member' => $memberID]);
        $this->memberRepository->insert(table:'members', values:[
            'username' => $request->username,
            'email' => $request->email,
            'password' => password_hash($request->password, PASSWORD_BCRYPT),
            'active' => $this->token,
            'permission' => 'user',
            'member_id' => $memberID
        ]);

        return [
            'username' => $request->username,
            'encryptedID' => base64_encode($memberID),
            'token' => $this->token,
            'recipient' => $request->email,
            'memberID' => $memberID,
            'url' => $_SERVER['HTTP_HOST']
        ];
    }

    public function activate(string $id = '', string $token = ''): Response
    {
        if (empty($id) || empty($token)) {
            return new Response('/index?message=', Messages::INVALID_URL);
        }

        $memberID = $this->validator->isBase64($id) ? base64_decode($id) : $id;
        $memberDB = $this->memberRepository->getMemberInfo('member_id', $memberID);
        $idDB = $memberDB['member_id'] ?? '';
        $tokenDB = $memberDB['active'] ?? '';
        if (strcmp($idDB, $memberID) == 0 && strcmp($token, $tokenDB) == 0) {
            $this->memberRepository->updateMembersTable([
                'active' => 'yes',
                'member_id' => $memberDB['member_id']
            ]);

            return new Response('/login?message=', Messages::REQUEST_ACTIVATE, '#login');
        }

        return new Response('/register?message=', Messages::REQUEST_ACTIVATE_FAIL, '#register');
    }

    public function proccesLogin(Request $request): Response
    {
        $active = $this->memberRepository->getMemberInfo('username', $request->username, 'active');

        if ($validate = $this->validator->validateLogin($request, $active)) {
            $_SESSION = ['old_username' => $request->username];

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
        $memberData = $this->memberRepository->getMemberInfo('username', $request->username);

        if (isset($request->remember)) {
            $id = $this->encryption->encrypt($_SERVER['REMOTE_ADDR']);
            $username = $this->encryption->encrypt($request->username);
            $userID = $username . '|' . $id;
            setcookie('remember', $userID, time() + (86400 * 7), '/');
        } else {
            $_SESSION['member'] = serialize($memberData);
        }
    }

    public function proccessResetToken(Request $request): array|Response
    {
        $memberRepository = $this->memberRepository;
        $validate = $this->validator->validateResetSend($request);

        if (isset($validate)) {
            @$_SESSION = ['old_email' => $request->email];

            return new Response('/?message=', $validate, '#reset');
        }

        $memberID = $memberRepository->getMemberInfo('email', $request->email, 'member_id');
        $memberRepository->updateMembersTable([
            'reset_token' => $this->token,
            'member_id' => $memberID
        ]);

        $memberID = $this->encryption->encrypt($memberID);

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
        $memberRepository = $this->memberRepository;
        $validate = $this->validator->validateResetSend($request);

        if (isset($validate)) {
            return new Response(
                '/reset?message=',
                sprintf(Messages::VALID_FORGOTTEN_USER, $request->email),
                '#reset'
            );
        }
        $username = $memberRepository->getMemberInfo('email', $request->email, 'username');
        $memberID = $this->encryption->encrypt(
            $memberRepository->getMemberInfo('email', $request->email, 'member_id')
        );

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
        $memberRepository = $this->memberRepository;
        $validate = $this->validator->validatePassword($request);

        if (isset($validate)) {
            return new Response('/?message=', $validate, '#newpassword');
        }

        $memberRepository->updateMembersTable([
            'password' => password_hash($request->password, PASSWORD_BCRYPT),
            'member_id' => $request->memberID
        ]);

        return new Response('/?message=', Messages::REQUEST_RESET_PASSWORD, '#login');
    }

    public function logout(): Response
    {
        $_SESSION = [];
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
        $memberRepository = $this->memberRepository;
        $memberRepository->deleteMember($memberID);
        $username = explode('|', $memberID);
        return new Response('/usertable?message=', sprintf(Messages::REQUEST_DELETE, $username[0]));
    }

    public function getMember(
        ?string $column = null,
        ?string $value = null,
        ?string $item = null
    ): mixed {
        return $this->memberRepository->getMemberInfo(
            column:$column,
            value:$value,
            item:$item
        );
    }

    public function update(Member $member): void
    {
        if ($this->memberRepository->exist($member->member_id)) {
            $this->memberRepository->updateMembersTable([
                "username" => $member->username,
                "email" => $member->email,
                "avatar" => $member->avatar,
                "active" => $member->active,
                "permission" => $member->permission,
                "reset_token" => $member->reset_token,
                "reset_complete" => $member->reset_complete,
                "member_id" => $member->member_id
            ]);
            $this->memberRepository->updateInfoTable($member);
        }
    }

    private function getMessageForType(string $type, string $replace): string
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
