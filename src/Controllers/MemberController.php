<?php

declare(strict_types=1);

namespace Mlkali\Sa\Controllers;

use Mlkali\Sa\Database\Repository\MemberRepository;
use Mlkali\Sa\Http\{Mailer, Request, Response};
use Mlkali\Sa\Security\{Encryption, Validator};
use Mlkali\Sa\Support\{Arr, Messages};

class MemberController
{
    public function __construct(
        private Encryption $encryption,
        private MemberRepository $memberRepository,
        private Mailer $mailer,
        private Validator $validator,
    ) {
    }

    public function register(Request $request): Response
    {
        $this->stroreOldInput(r:$request);

        if ($err = $this->validator->validateRegister(r:$request)) {
            return new Response('/regiter?message=', $err, '#register');
        }

        $memberId = base64_encode("{$request->username}|{$request->email}");

        if ($this->memberRepository->exist(id:$memberId)) {
            return new Response(
                '/register?message=',
                sprintf(Messages::DANGER_USER_ALREADY_EXISTS, $request->username),
                '#register'
            );
        }

        $data = $this->prepareRegistrationData(r:$request, id:$memberId);

        $this->persistMember(d:$data);
        $this->sendMail(t:'activate', d:$data);

        return new Response(
            "/register?message=",
            sprintf(Messages::SUCCESS_REGISTER, Arr::pick($data, ['email'])),
            '#register'
        );
    }

    public function activate(string $id = '', string $token = ''): Response
    {
        if (empty($id) || empty($token)) {
            return new Response('/index?message=', Messages::DANGER_INVALID_URL);
        }

        $memberID = $this->validator->isBase64($id) ? base64_decode($id) : $id;
        $db = $this->memberRepository->getMemberInfo('member_id', $memberID);

        if (
            ($db['member_id'] ?? '') !== $memberID
            && ($db['active'] ?? '') !== $token
        ) {
            return new Response('/register?message=', Messages::DANGER_ACTIVATE_FAIL, '#register');
        }

        return new Response('/login?message=', Messages::SUCCESS_ACTIVATE, '#login');
    }

    public function login(Request $request): Response
    {
        $this->stroreOldInput(r:$request);

        $user = $this->memberRepository->getMemberInfo(
            column:'username',
            value:$request->username,
        );

        if ($validate = $this->validator->validateLogin($request, $user['active'] ?? null)) {
            return new Response('/login?message=', $validate, '#login');
        }

        $this->setMember(Arr::except($user, ['password']));

        return new Response("/member/{$request->username}", null, '#member');
    }

    public function lostPassword(Request $request): Response
    {
        $this->stroreOldInput(r:$request);

        if ($validate = $this->validator->validateResetSend($request)) {
            return new Response('/reset?message=', $validate, '#reset');
        }

        $name = $request->username ?? $request->email;

        if (isset($request->username)) {
            $member = $this->memberRepository->getMemberInfo(
                column:'username',
                value:$request->username,
            );
        } elseif (isset($request->email)) {
            $member = $this->memberRepository->getMemberInfo(
                column:'email',
                value:$request->email,
            );
        }

        if (!is_array($member)) {
            return new Response(
                '/reset?message=',
                sprintf(Messages::DANGER_FORGOTTEN_USER, $name),
                '#reset'
            );
        }

        $this->sendMail(t:'reset', d:$member);

        return new Response(
            '/login?message=',
            sprintf(Messages::SUCCESS_RESET_SEND, $member['email']),
            '#login'
        );
    }

    # lostPassword + forgottenUser SHOULD BE combined
    public function forgottenUser(Request $request)
    {
        $this->stroreOldInput(r:$request);

        if ($this->validator->validateResetSend($request)) {
            return new Response(
                'login?message=',
                sprintf(Messages::DANGER_FORGOTTEN_USER, $request->email),
                '#login'
            );
        }

        $this->sendMail(t:'user', d:[]);
    }

    public function setNewPassword(Request $request): Response
    {
        if ($validate = $this->validator->validatePassword($request)) {
            return new Response('/message=', $validate, '#newpassword');
        }

        $this->memberRepository->updateMembersTable([
            'password' => password_hash($request->password, PASSWORD_BCRYPT),
            'member_id' => $request->memberID
        ]);

        return new Response(
            'login?message=',
            Messages::SUCCESS_RESET_PASSWORD,
            '#login'
        );
    }

    //TODO view page need be changed
    public function updateMember(Request $request): Response
    {
        $this->stroreOldInput(r:$request);

        if ($validate = $this->validator->validateAvatar($request)) {
            return new Response(
                "/member/{$request->username}/update?message=",
                $validate,
                '#member'
            );
        }

        $allowedTypes = [
            'image/png' => 'png',
            'image/jpeg' => 'jpeg',
            'image/jpg' => 'jpg'
        ];

        $extension = $allowedTypes[$request->avatar['type']];
        $uploadName = htmlspecialchars($request->avatar['name'], ENT_QUOTES, 'UTF-8') . '.' . $extension;
        $tagetDir = Arr::$path . 'public' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'avatars';
        $newFilePath = $tagetDir . $request->avatar['name'] . '.' . $extension;

        move_uploaded_file($request->avatar['tmp_name'], $newFilePath);
        unlink($request->avatar['tmp_name']);

        $this->memberRepository->updateMembersTable([]);
        $this->memberRepository->updateInfoTable([]);

        return new Response(
            "/member/{$request->username}?message=",
            sprintf(Messages::SUCCESS_UPDATED, [$request->username, ':-)']),
            '#member'
        );
    }

    //TODO: Admin page will change redirect will change /
    public function delete(string $memberID): Response
    {
        // only admin has access to this function
        if ($this->memberRepository->exist(id:$memberID)) {
            $this->memberRepository->delete(id:$memberID);
            return new Response(
                '/index?message=',
                sprintf(Messages::SUCCESS_DELETE, $memberID),
                '#index'
            );
        }

        return new Response(
            '/index?message=',
            sprintf(Messages::DANGER_USER_NOT_EXIST, $memberID),
            '#index'
        );
    }

    public function member(string $memberID = '', array $fetch = [])
    {
        $column = $memberID === '' ? null : 'member_id';
        return $this->memberRepository->getMemberInfo(
            column:$column,
            value:$memberID,
            item:$fetch
        );
    }

    private function stroreOldInput(Request $r): void
    {
        $_SESSION['old_username'] = $r->username ?? '';
        $_SESSION['old_email'] = $r->email ?? '';
    }

    private function prepareRegistrationData(Request $r, string $id): array
    {
        return [
            'username'   => $r->username,
            'email'      => $r->email,
            'password'   => password_hash($r->password, PASSWORD_BCRYPT),
            'active'     => $this->encryption->token(),
            'permission' => 'user',
            'member_id'  => $id,
            'url'        => $_SERVER['HTTP_HOST'],
        ];
    }

    private function persistMember(array $d): void
    {
        $this->memberRepository->insert(
            table:'info',
            values:Arr::pick($d, ['member_id'])
        );

        $this->memberRepository->insert(
            table:'members',
            values:Arr::except($d, ['url'])
        );
    }

    private function sendMail(string $t, array $d): void
    {
        $emailData = $this->mailer->getEmailData(template:$t, data:$d);
        $this->mailer->sender(
            body: $emailData['body'],
            subject: $emailData['subject'],
            to: $emailData['to']
        );
    }

    private function setMember(array $d): void
    {
        if (!isset($d['remember'])) {
            $_SESSION['member'] = serialize($d);
        } else {
            //TODO: rember user
        }
    }
}
