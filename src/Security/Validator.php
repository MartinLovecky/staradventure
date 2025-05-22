<?php

declare(strict_types=1);

namespace Mlkali\Sa\Security;

use Mlkali\Sa\Http\Request;
use Mlkali\Sa\Support\Messages;

class Validator
{
    public function validateRegister(Request $r): ?string
    {
        if (!$this->commonValidation($r)) {
            return 'danger_CSRF validation failed';
        }
        if ($r->vops !== 'on' && $r->terms !== 'on') {
            return Messages::DANGER_REG_CHECKBOX_FAIL;
        }
        if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $r->email)) {
            return sprintf(Messages::DANGER_EMAIL_FORMAT, $r->email);
        }
        if (mb_strlen($r->username) < 4) {
            return sprintf(Messages::DANGER_LEN_USER, $r->username);
        }
        if ($r->password != $r->password_again) {
            return Messages::DANGER_PASSWORD_AGAIN;
        }
        return $this->validatePassword(r:$r);
    }

    public function validatePassword(Request $r): ?string
    {
        if (mb_strlen($r->password) < 6) {
            return Messages::DANGER_LEN_PASSWORD;
        }
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@$%^&*]).*$/', $r->password)) {
            return Messages::DANGER_PASSWORD_REGEX;
        }
        return null;
    }

    public function validateLogin(Request $r): ?string
    {
        if (!$this->commonValidation($r)) {
            return 'danger_CSRF validation failed';
        }
        if (!is_string($r->active)) {
            return Messages::DANGER_ACTIVE_MEMBER;
        }
        if (strcmp($r->active, 'yes') !== 0) {
            return Messages::DANGER_ACTIVE_MEMBER;
        }
        if (!$this->validToken($r->token)) {
            return Messages::DANGER_CSRF_ERROR;
        }
        return $this->validatePassword(r:$r);
    }

    public function validateForgotten(Request $r): ?string
    {
        if (!$this->commonValidation($r)) {
            return 'danger_CSRF validation failed';
        }
        if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $r->email)) {
            return sprintf(Messages::DANGER_EMAIL_FORMAT, $r->email);
        }
        if ($r->reset === 'username' || $r->reset === 'password') {
            return null;
        } else {
            return 'danger_Invalid reset action';
        }
        return null;
    }

    public function validateUpdate(Request $r): ?string
    {
        if (!$this->commonValidation($r)) {
            return 'danger_CSRF validation failed';
        }
        if (isset($request->avatar)) {
            return $this->validateAvatar($r);
        }
        if (isset($request->password)) {
            return $this->validatePassword($r);
        }

        return null;
    }

    public function validateAvatar(Request $request): ?string
    {
        if (!isset($request->avatar['tmp_name']) || !is_uploaded_file($request->avatar['tmp_name'])) {
            return Messages::DANGER_AVATAR_UPLOAD;
        }
        if (!isset($request->avatar['name'])) {
            return Messages::DANGER_AVATAR_UPLOAD;
        }
        if ($request->avatar['size'] === 0) {
            return Messages::DANGER_AVATAR_UPLOAD;
        }
        if ($request->avatar['size'] > 5145728) {
            return Messages::DANGER_AVATAR_SIZE;
        }
        $allowedMimeTypes = ['png', 'jpg', 'jpeg'];
        if (!in_array(pathinfo($request->avatar['name'], PATHINFO_EXTENSION), $allowedMimeTypes)) {
            return Messages::DANGER_AVATAR_MIME_TYPE;
        }
        return null;
    }

    public function isBase64(string $str): bool
    {
        return preg_match('/^(?:[A-Za-z0-9+\/]{4})*(?:[A-Za-z0-9+\/]{2}==|[A-Za-z0-9+\/]{3}=)?$/', $str) !== false;
    }

    private function commonValidation(Request $request): bool
    {
        return $this->validateCaptcha($request->grecaptcharesponse)
            && $this->validToken($request->token);
    }

    private function validateCaptcha(?string $response): mixed
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'secret' => $_ENV['PRIVATE'],
            'response' => $response
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($response, true);

        return $res['error-codes'] ?? true;
    }

    /**
     * Validates CSRF token.
     *
     * @param string $token CSRF token to validate.
     *
     * @return bool Returns true if the token is valid, otherwise false.
     */
    public function validToken(string $token): bool
    {
        $encryption = new Encryption();
        $og = explode('|', $_ENV['CSRFKEY']);
        $decrypted = $encryption->decrypt($token, $og[1]);

        return $_ENV['CSRFKEY'] === $decrypted;
    }
}
