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
        $this->storeOldInput(r:$request);
        $request->memberID = "{$request->username}|{$request->email}";
        $err = $this->validator->validateRegister(r:$request);

        if (is_string($err)) {
            return new Response('/register?message=', $err, '#register');
        }

        if (
            $this->findMember('email', $request->email)
            || $this->findMember('username', $request->username)
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
        $db = $this->ensureMemberExists('member_id', $memberID, 'index');

        if ($db instanceof Response) {
            return $db;
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
        $this->storeOldInput(r:$request);

        $column = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $db = $this->ensureMemberExists($column, $request->username, 'login');

        if ($db instanceof Response) {
            return $db;
        }

        $request->active = $db['active'];
        $request->username = $db['username'];
        $request->email = $db['email'];

        $validate = $this->validator->validateLogin(r:$request);

        if (is_string($validate)) {
            return new Response('/login?message=', $validate, '#login');
        }

        $remember = $request->remember ?? false;
        $this->setMember(array_merge($db, ['remember' => $remember]));

        return new Response("/member/{$request->username}", null, '#member');
    }

    public function loginWithRememberCookie(): ?array
    {
        if (empty($_COOKIE['remember_me'])) {
            return null;
        }

        $token = $_COOKIE['remember_me'];
        $member = $this->findMember('remember_token', $token);

        if ($member) {
            $_SESSION['member'] = serialize($member);
            return $member;
        }

        setcookie('remember_me', '', time() - 3600, '/', '', false, true);
        return null;
    }

    public function logout(): void
    {
        if (isset($_SESSION['member'])) {
            $member = unserialize($_SESSION['member']);
            if ($member && isset($member['member_id'])) {
                $this->memberRepository->updateMembersTable(
                    ['remember_token' => null],
                    $member['member_id']
                );
            }
        }

        setcookie('remember_me', '', time() - 3600, '/', '', false, true);
        session_destroy();
    }

    public function forgoten(Request $request): Response
    {
        $this->storeOldInput(r:$request);
        $err = $this->validator->validateForgotten(r:$request);

        if (is_string($err)) {
            return new Response('/reset?message=', $err, '#reset');
        }

        $db = $this->ensureMemberExists('email', $request->email, 'reset');

        if ($db instanceof Response) {
            return $db;
        }

        $this->mailer->sendMail(t:'reset', d:[
            'url' => $_SERVER['HTTP_HOST'],
            'username' => $db['username'],
            'member_id' => $db['member_id'],
            'active' => $this->encryption->token(),
            'email' => $request->email
        ]);

        return new Response(
            '/reset?message=',
            sprintf(Messages::SUCCESS_RESET_SEND, $db['email']),
            '#reset'
        );
    }

    public function updateMember(Request $request): Response
    {
        $this->storeOldInput($request);
        $validate = $this->validator->validateUpdate($request);

        if (is_string($validate)) {
            return new Response('/index?message=', $validate, '#index');
        }

        $db = $this->ensureMemberExists('username', $request->username, 'member');

        if ($db instanceof Response) {
            return $db;
        }

        $avatar = $this->processAvatar($request);
        $pwd = $request->password ? password_hash($request->password, PASSWORD_BCRYPT) : $this->member->password;
        $permission = $request->permission ?? $this->member->permission;

        $this->memberRepository->updateMembersTable([
            'username' => $request->username ?? $this->member->username,
            'email' => $request->email ?? $this->member->email,
            'password' => $pwd,
            'avatar' => $avatar,
            'permission' => $permission,
        ], $db['member_id']);

        $this->memberRepository->updateInfoTable([
            'member_name' => $request->name ?? null,
            'member_surname' => $request->surname ?? null,
            'visible' => $request->visible ?? 0,
            'age' => $request->ageDate ?? null,
        ], $db['member_id']);

        return new Response('/member?message=', Messages::SUCCESS_UPDATED, '#member');
    }

    public function delete(Request $request): Response
    {
        $db = $this->ensureMemberExists('member_id', $request->memberID, 'admin');

        if ($db instanceof Response) {
            return $db;
        }

        $this->memberRepository->delete($request->memberID);

        return new Response(
            '/admin?message=',
            sprintf(Messages::SUCCESS_DELETE, $request->memberID),
            '#admin'
        );
    }

    public function permission(string $value, string $memberID): Response
    {
        $db = $this->ensureMemberExists('member_id', $memberID, 'admin');

        if ($db instanceof Response) {
            return $db;
        }

        $this->memberRepository->updateMembersTable(['permission' => $value], $memberID);
        $this->setMember($db);

        return new Response(
            'admin?message=',
            Messages::SUCCESS_PERMISSION,
            '#admin'
        );
    }

    /**
     * @return array|string|false|null
     */
    public function findMember(mixed ...$args): mixed
    {
        [$c, $v] = $args + [null, null];
        $i = count($args) > 2 ? Arr::removeIndexes($args, [0, 1]) : [];
        return $this->memberRepository->getMemberInfo(column: $c, value: $v, item: $i);
    }

    public function ensureMemberExists(
        string $column,
        string $value,
        string $action
    ): Response|array {
            $db = $this->findMember($column, $value);
        if (!$db) {
            return new Response(
                "/{$action}?message=",
                sprintf(Messages::DANGER_USER_NOT_EXIST, $value),
                "#{$action}"
            );
        }

            return $db;
    }

    private function storeOldInput(Request $r): void
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

    private function processAvatar(Request $r): string
    {
        if (empty($r->avatar['tmp_name'])) {
            return 'empty_profile.png';
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $r->avatar['tmp_name']);
        finfo_close($finfo);

        $allowedTypes = [
            'image/png'  => 'png',
            'image/jpeg' => 'jpeg',
            'image/jpg'  => 'jpg'
        ];

        if (!array_key_exists($mime, $allowedTypes)) {
            throw new \RuntimeException("Invalid avatar file type: $mime");
        }

        $extension = $allowedTypes[$mime];
        $safeName = pathinfo($r->avatar['name'], PATHINFO_FILENAME);
        $filename = htmlspecialchars($safeName, ENT_QUOTES, 'UTF-8') . '.' . $extension;

        $targetDir = Arr::$path . 'public' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'avatars';
        $newFilePath = $targetDir . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file($r->avatar['tmp_name'], $newFilePath)) {
            throw new \RuntimeException("Failed to upload avatar");
        }

        return $filename;
    }

    private function persistMember(array $d): void
    {
        $this->memberRepository->insert('info', Arr::pick($d, ['member']));
        $this->memberRepository->insert('members', Arr::except($d, ['url', 'member']));
    }

    private function setMember(array $d): void
    {
        if (!isset($d['remember'])) {
            $_SESSION['member'] = serialize($d);
        } else {
            $token = $this->encryption->token();
            $this->memberRepository->updateMembersTable(
                ['remember_token' => $token],
                $d['member_id']
            );
            setcookie(
                'remember_me',
                $token,
                time() + 60 * 60 * 24 * 30,
                '/',
                '',
                false,
                true
            );

            $_SESSION['member'] = serialize($d);
        }
    }
}
