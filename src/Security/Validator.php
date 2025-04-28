<?php

declare(strict_types=1);

namespace Mlkali\Sa\Security;

use Mlkali\Sa\Http\Request;
use Mlkali\Sa\Support\Messages;

class Validator
{
    /**
     * Validates registration input data.
     *
     * @param Request $request The HTTP request containing registration data.
     *
     * @return string|null Returns an error message if validation fails, or null if validation passes.
     */
    public function validateRegister(Request $request): ?string
    {
        if (!$this->commonValidation($request)) {
            return 'danger_CSRF validation failed';
        }
        if ($request->vops !== 'on' && $request->terms !== 'on') {
            return Messages::VALID_REG_CHECKBOX_FAIL;
        }
        if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $request->email)) {
            return sprintf(Messages::VALID_EMAIL_FORMAT, $request->email);
        }
        if (mb_strlen($request->username) < 4) {
            return sprintf(Messages::VALID_LEN_USER, $request->username);
        }
        return $this->validatePassword($request);
    }

    public function validateLogin(Request $request, ?string $active = null): ?string
    {
        if (!is_string($active)) {
            return Messages::VALID_ACTIVE_MEMBER;
        }
        if (!$this->commonValidation($request)) {
            return 'danger_CSRF validation failed';
        }
        if (strcmp($active, 'yes') !== 0) {
            return Messages::VALID_ACTIVE_MEMBER;
        }
        if (!$this->validToken($request->token)) {
            return Messages::VALID_CSRF_ERROR;
        }
        return null;
    }

    /**
     * Validates reset password request data.
     *
     * @param Request $request The HTTP request containing reset password data.
     *
     * @return string|null Returns an error message if validation fails, or null if validation passes.
     */
    public function validateResetSend(Request $request): ?string
    {
        if (!$this->commonValidation($request)) {
            return 'danger_CSRF validation failed';
        }
        return $this->validatePassword($request);
    }

    /**
     * Validates avatar upload data.
     *
     * @param Request $request The HTTP request containing avatar upload data.
     *
     * @return string|null Returns an error message if validation fails, or null if validation passes.
     */
    public function validateAvatar(Request $request): ?string
    {
        if (!$this->commonValidation($request)) {
            return 'danger_CSRF validation failed';
        }
        if (!isset($request->avatar['tmp_name']) || !is_uploaded_file($request->avatar['tmp_name'])) {
            return Messages::AVATAR_UPLOAD;
        }
        if (!isset($request->avatar['name'])) {
            return Messages::AVATAR_UPLOAD;
        }
        if ($request->avatar['size'] === 0) {
            return Messages::AVATAR_UPLOAD;
        }
        if ($request->avatar['size'] > 5145728) {
            return Messages::AVATAR_SIZE;
        }
        $allowedMimeTypes = ['png', 'jpg', 'jpeg'];
        if (!in_array(pathinfo($request->avatar['name'], PATHINFO_EXTENSION), $allowedMimeTypes)) {
            return Messages::AVATAR_MIME_TYPE;
        }
        return null;
    }

    /**
     * Validates password data.
     *
     * @param Request $request The HTTP request containing new password data.
     *
     * @return string|null Returns an error message if validation fails, or null if validation passes.
     */
    public function validatePassword(Request $request): ?string
    {
        if (mb_strlen($request->password) < 6) {
            return Messages::VALID_LEN_PASSWORD;
        }
        if ($request->password != $request->password_again) {
            return Messages::VALID_PASSWORD_AGAIN;
        }
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@$%^&*]).*$/', $request->password)) {
            return Messages::VALID_PASSWORD_REGEX;
        }
        return null;
    }

    /**
     * check if string is Base64 encoded
     *
     * @param string $str
     *
     * @return bool
     */
    public function isBase64(string $str): bool
    {
        return preg_match('/^(?:[A-Za-z0-9+\/]{4})*(?:[A-Za-z0-9+\/]{2}==|[A-Za-z0-9+\/]{3}=)?$/', $str) !== false;
    }

    /**
     * Performs common validation checks for related requests.
     *
     * This method consolidates common validation checks including CAPTCHA validation and CSRF token validation.
     *
     * @param Request $request containing the data to be validated.
     *
     * @return bool
     *
     */
    private function commonValidation(Request $request): bool
    {
        return $this->validateCaptcha($request->grecaptcharesponse)
            && $this->validToken($request->token);
    }


    /**
     * Validates CAPTCHA response with Google's reCAPTCHA API.
     *
     * @param ?string $response The CAPTCHA response from the user.
     *
     * @return mixed Returns an error message, or true if validation passes.
     */
    private function validateCaptcha(?string $response): mixed
    {
        return $response;
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
