<?php

namespace Mlkali\Sa\Controllers;

use Mlkali\Sa\Http\Request;
use Mlkali\Sa\Http\Response;
use Mlkali\Sa\Support\Validator;
use Mlkali\Sa\Controllers\MemberController;
use Mlkali\Sa\Support\Messages;

class RequestController
{

    public function __construct(
        private Validator $validator,
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

        return new Response('/login?message=', sprintf(Messages::REQUETS_REGISTER, $request->email), '#login');
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

            return new Response('member/' . $request->username . '?message=', sprintf(Messages::REQUETS_REGISTER, $request->username));
        }

        $this->memberController->login($request->username);

        return new Response('member/' . $request->username . '?message=', sprintf(Messages::REQUETS_REGISTER, $request->username));
    }

    public function submitResetSend(Request $request): Response
    {
        $validate = $this->validator->validateResetSend($request);

        if (isset($validate)) {
            @$_SESSION = ['old_email' => $request->email];

            return new Response('/?message=', $validate, '#reset');
        }

        $this->memberController->sendResetToken($request);

        return new Response('/?message=', sprintf(Messages::REQUETS_RESET_SEND, $request->email), '#');
    }

    public function setNewPassword(Request $request): Response
    {
        $validate = $this->validator->validatePassword($request);

        if (isset($validate)) {
            return new Response('/?message=', $validate, '#newpassword');
        }

        $this->memberController->setNewPassword($request);

        return new Response('/?message=', Messages::REQUETS_RESET_PASSWORD, '#login');
    }

    public function submitForgottenUser(Request $request): Response
    {
        $validate = $this->validator->validateResetSend($request);

        if (isset($validate)) {
            return new Response('/reset?message=', sprintf(Messages::VALIDATION_FORGOTEN_USER, $request->email), '#reset');
        }

        $this->memberController->sendForgottenUser($request);

        return new Response('/login?message=', sprintf(Messages::REQUETS_FORGOTEN_USER, $request->email), '#login');
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
