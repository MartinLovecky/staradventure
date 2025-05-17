<?php

declare(strict_types=1);

namespace Mlkali\Sa\Controllers;

use Mlkali\Sa\Database\Entity\Member;
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
        private Member $member,
    ) {
    }

    public function register(Request $request): Response
    {
        $this->stroreOldInput(r:$request);
        $request->memberID = "{$request->username}|{$request->email}";

        if ($err = $this->validator->validateRegister(r:$request)) {
            return new Response('/regiter?message=', $err, '#register');
        }

        if (
            $this->member('email', $request->email)
            || $this->member('username', $request->username)
        ) {
            return new Response(
                '/register?message=',
                sprintf(Messages::DANGER_USER_ALREADY_EXISTS, $request->username),
                '#register'
            );
        }

        $data = $this->prepareRegistrationData(r:$request);

        $this->persistMember(d:$data);
        $this->mailer->sendMail(t:'activate', d:$data);

        return new Response(
            "/register?message=",
            sprintf(Messages::SUCCESS_REGISTER, Arr::pick($data, ['email'])),
            '#register'
        );
    }

    public function activate(string $memberID = '', string $token = ''): Response
    {
        if (!$db = $this->member('member_id', $memberID)) {
            return new Response(
                '/index?message=',
                sprintf(Messages::DANGER_USER_NOT_EXIST, $memberID)
            );
        }

        if (
            $db['member_id'] !== $memberID
            && $db['active'] !== $token
        ) {
            return new Response(
                '/register?message=',
                Messages::DANGER_ACTIVATE_FAIL,
                '#register'
            );
        }

        $this->memberRepository->updateMembersTable(['active' => 'yes'], $memberID);

        return new Response('/login?message=', Messages::SUCCESS_ACTIVATE, '#login');
    }

    public function login(Request $request): Response
    {
        $this->stroreOldInput(r:$request);

        $column = preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $request->username)
            ? 'email'
            : 'username';

        if (
            !$user = $this->memberRepository->getMemberInfo(
                column:$column,
                value:$request->username
            )
        ) {
            return new Response(
                '/login?message=',
                sprintf(Messages::DANGER_USER_NOT_EXIST, $request->username . '|' . $request->email),
                '#login'
            );
        }

        $request->active = $user['active'];
        $request->username = $user['username'];
        $request->email = $user['email'];

        if ($validate = $this->validator->validateLogin(r:$request)) {
            return new Response('/login?message=', $validate, '#login');
        }

        $this->setMember($user);

        return new Response("/member/{$request->username}", null, '#member');
    }

    public function forgoten(Request $request): Response
    {
        $this->stroreOldInput(r:$request);

        if ($validate = $this->validator->validateFogoten(r:$request)) {
            return new Response('/reset?message=', $validate, '#reset');
        }

        if (!$db = $this->member('email', $request->email)) {
            return new Response(
                '/index?message=',
                sprintf(Messages::DANGER_USER_NOT_EXIST, $request->email)
            );
        }

        $this->mailer->sendMail(t:'reset', d:[
            'url' => $_SERVER['HTTP_HOST'],
            'username' => $db['username'],
            'member_id' => $db['member_id'],
            'active' => $this->encryption->token()
        ]);

        return new Response(
            '/reset?message=',
            sprintf(Messages::SUCCESS_RESET_SEND, $db['email']),
            '#reset'
        );
    }

    public function updateMember(Request $request)
    {
        $this->stroreOldInput(r:$request);

        if ($validate = $this->validator->validateUpdate(r:$request)) {
            return new Response('/index?message=', $validate, '#index');
        }

        //TODO GET MemberID

        $avatar = $this->processAvatar(r: $request);
        $pwd = $request->password ? password_hash($request->password, PASSWORD_BCRYPT) : $this->member->password;
        $permission = isset($request->permission) ? $this->member->permission : $request->permission;

        $this->memberRepository->updateMembersTable([
            'username' => $request->username ?? $this->member->usernme,
            'email' => $request->email ?? $this->member->email,
            'password' => $pwd,
            'avatar' => $avatar,
            'permission' => $permission,
            ], 'nocllue');

        $this->memberRepository->updateInfoTable([
            'member_name' => $request->name ?? null,
            'member_surname' => $request->surname ?? null,
            'visible' => $request->visible ?? 0,
            'age' => $request->ageDate ?? null,
        ], 'yep');
    }

    private function processAvatar(Request $r): string
    {
        if (!isset($r->avatar)) {
            return 'empty_profile.png';
        }

        $allowedTypes = [
            'image/png' => 'png',
            'image/jpeg' => 'jpeg',
            'image/jpg' => 'jpg'
        ];

        $extension = $allowedTypes[$r->avatar['type']];
        $uploadName = htmlspecialchars($r->avatar['name'], ENT_QUOTES, 'UTF-8') . '.' . $extension;
        $tagetDir = Arr::$path . 'public' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'avatars';
        $newFilePath = $tagetDir . $r->avatar['name'] . '.' . $extension;

        move_uploaded_file($r->avatar['tmp_name'], $newFilePath);
        unlink($r->avatar['tmp_name']);

        return $uploadName;
    }

    public function delete(Request $request): Response
    {
        $id = explode('|', $request->memberID);
        $request->username = $id[0] ?? '';
        $request->email = $id[1] ?? '';

        if (
            $this->member('username', $request->username)
            || $this->member('email', $request->email)
        ) {
            $this->memberRepository->delete($request->memberID);
            return new Response(
                '/admin?message=',
                sprintf(Messages::SUCCESS_DELETE, $request->memberID),
                '#admin'
            );
        }

        return new Response(
            '/admin?message=',
            sprintf(Messages::DANGER_USER_NOT_EXIST, $request->memberID),
            '#admin'
        );
    }

    //TODO
    public function permission(string $value, string $memberID): mixed
    {
        $this->memberRepository->updateMembersTable(
            ['permission' => $value],
            $memberID
        );

        $memberData = $this->member('member_id', $memberID);

        $this->setMember($memberData);

        return $this->member;
    }

    /**
     *
     * @param mixed ...$args
     * @return mixed
     */
    public function member(mixed ...$args): mixed
    {
        $c = count($args) === 0 ? null : $args[0];
        $v = $args[1] ?? null;
        $i = !isset($args[2]) ? [] : Arr::removeIndexes($args, [0, 1]);

        return $this->memberRepository->getMemberInfo(
            column:$c,
            value:$v,
            item:$i
        );
    }

    private function stroreOldInput(Request $r): void
    {
        $_SESSION['old_username'] = $r->username ?? '';
        $_SESSION['old_email'] = $r->email ?? '';
    }

    private function prepareRegistrationData(Request $r): array
    {
        return [
            'username'   => $r->username,
            'email'      => $r->email,
            'password'   => password_hash($r->password, PASSWORD_BCRYPT),
            'active'     => $this->encryption->token(),
            'permission' => 'user',
            'member_id'  => $r->memberID,
            'member'     => $r->memberID,
            'url'        => $_SERVER['HTTP_HOST'],
        ];
    }

    private function persistMember(array $d): void
    {
        $this->memberRepository->insert(
            table:'info',
            values:Arr::pick($d, ['member'])
        );

        $this->memberRepository->insert(
            table:'members',
            values:Arr::except($d, ['url', 'member'])
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
