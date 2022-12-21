<?php

namespace Mlkali\Sa\Controllers;

use Mlkali\Sa\Http\Request;
use Mlkali\Sa\Http\Response;
use Mlkali\Sa\Support\Validator;
use Mlkali\Sa\Controllers\MemberController;
use Mlkali\Sa\Database\Entity\Member;

class RequestController
{

    public function __construct(
        private Validator $validator,
        private Member $member,
        private MemberController $memberController,
    ) {
    }

    public function submitRegister(Request $request): Response
    {
        $validate = $this->validator->validateRegister($request);

        if (isset($validate)) {

            @$_SESSION = [
                'old_username' => $request->username,
                'old_email' => $request->email
            ];

            return new Response('/register?message=', $validate, '#register');
        }

        $this->memberController->register($request);

        return new Response('/login?message=', 'success.Byl vám odeslán aktivační email (zkontrolujte prosím i spam)', '#login');
    }

    public function submitLogin(Request $request): Response
    {
        $validate = $this->validator->validateLogin($request);

        if (isset($validate)) {
            @$_SESSION = ['old_username' => $request->username];

            return new Response('/login?message=', $validate, '#login');
        }
        if (isset($request->remember)) {

            setcookie('remember', $request->username, time() + (86400 * 7), '/');

            return new Response('member/' . $request->username . '?message=', 'success.Vítejte zpět ' . $request->username);
        } else {
            $this->memberController->login($request->username);

            return new Response('member/' . $request->username . '?message=', 'success.Vítejte zpět ' . $request->username);
        }
    }

    public function submitResetSend(Request $request): Response
    {
        $validate = $this->validator->validateResetSend($request);

        if (isset($validate)) {
            @$_SESSION = ['old_email' => $request->email];

            return new Response('/?message=', $validate, '#reset');
        }

        $this->member->sendResetToken($request);

        return new Response('/?message=', 'success.Odkaz na změnu hesla byl odeslán na email', '#');
    }

    public function setNewPassword(Request $request): Response
    {
        $validate = $this->validator->validatePassword($request);

        if (isset($validate)) {
            return new Response('/?message=', $validate, '#newpassword');
        }

        $this->member->setNewPassword($request);

        return new Response('/?message=', 'success.Heslo bylo úspěšně změněno', '#login');
    }

    public function submitForgottenUser(Request $request): Response
    {
        $validate = $this->validator->validateResetSend($request);

        if (isset($validate)) {
            return new Response('/reset?message=', 'danger.Neplatný email (' . $request->email . ')', '#reset');
        }

        $this->member->sendForgottenUser($request->email);

        return new Response('/login?message=', 'succes.Uživatelské jméno bylo zaslíno na váš email', '#login');
    }

    public function updateMember(Request $request): Response
    {
        $filePath = $_FILES['avatar']['tmp_name'];
        $fileType = mime_content_type($filePath);
        $fileName = basename($filePath);
        $fileSize = filesize($filePath);

        $validate = $this->validator->validateAvatar($filePath, $fileType, $fileSize, $request);

        if (isset($validate)) {
            return new Response('/reset?message=', $validate, '#updatemember');
        }

        $allowedTypes = [
            'image/png' => 'png',
            'image/jpeg' => 'jpeg',
            'image/jpg' => 'jpg'
        ];

        $extension = $allowedTypes[$fileType];
        $uploadName = $fileName . '.' . $extension;
        $targetDir = $_SERVER['DOCUMENT_ROOT'] . '/public/img/avatars/';

        $newFilePath = $targetDir . $fileName . '.' . $extension;

        move_uploaded_file($filePath, $newFilePath);
        unlink($filePath);

        $this->memberController->update($request, $uploadName);

        return new Response('/member' . $request->username . '?message=', 'succes.Informace upraveny');
    }
}
