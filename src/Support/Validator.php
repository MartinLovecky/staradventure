<?php

namespace Mlkali\Sa\Support;

use Mlkali\Sa\Http\Request;
use Mlkali\Sa\Support\Messages;
use Mlkali\Sa\Database\Repository\MemberRepository;

class Validator
{
    public function __construct(
        public MemberRepository $memberRepository,
        public Encryption $encryption
    ) {
    }

    /**
     * Validates registration input data.
     *
     * @param Request $request The HTTP request containing registration data.
     *
     * @return string|null Returns an error message if validation fails, or null if validation passes.
     */
    public function validateRegister(Request $request): ?string
    {
        $validationError = $this->commonValidation($request);
        if ($validationError) {
            return $validationError;
        }
        if ($request->vops !== 'on' && $request->terms !== 'on') {
            return Messages::VALIDATION_REG_CHECKBOX_FAIL;
        }
        if ($this->memberRepository->getMemberInfo('member_id', $request->username . '|' . $request->email)) {
            return sprintf(Messages::VALIDATION_USER_ALREADY_EXISTS, $request->username);
        }
        // email validation structure
        if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $request->email)) {
            return sprintf(Messages::VALIDATION_EMAIL_FORMAT, $request->email);
        }
        if (mb_strlen($request->username) < 4) {
            return sprintf(Messages::VALIDATION_LEN_USER, $request->username);
        }
        return $this->validatePassword($request);
    }

    /**
     * Validates login input data.
     *
     * @param Request $request The HTTP request containing login data.
     * @param string $activeMember The activation status of the member ('yes' or other values).
     *
     * @return string|null Returns an error message if validation fails, or null if validation passes.
     */
    public function validateLogin(Request $request, string $activeMember): ?string
    {
        $validationError = $this->commonValidation($request);
        if ($validationError) {
            return $validationError;
        }
        if (strcmp($activeMember, 'yes') !== 0) {
            return Messages::VALIDATION_ACTIVE_MEMBER;
        }
        if (!$this->validToken($request->token)) {
            return Messages::VALIDATION_CSRF_ERROR;
        }
        if (!$this->memberRepository->getMemberInfo('username', $request->username, 'username')) {
            return sprintf(Messages::VALIDATION_USER_NOT_EXIST, $request->username);
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
        $validationError = $this->commonValidation($request);
        if ($validationError) {
            return $validationError;
        }
        if (!$this->memberRepository->getMemberInfo('email', $request->email, 'email')) {
            return sprintf(Messages::VALIDATION_USER_NOT_EXIST, $request->email);
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
        $validationError = $this->commonValidation($request);
        if ($validationError) {
            return $validationError;
        }
        if (!isset($avatar['tmp_name']) || !is_uploaded_file($request->avatar['tmp_name'])) {
            return Messages::AVATAR_UPLOAD;
        }
        if (!isset($avatar['name'])) {
            return Messages::AVATAR_UPLOAD;
        }
        if ($avatar['size'] === 0) {
            return Messages::AVATAR_UPLOAD;
        }
        if ($avatar['size'] > 5145728) {
            return Messages::AVATAR_SIZE;
        }
        $allowedMimeTypes = ['png', 'jpg', 'jpeg'];
        if (!in_array(pathinfo($avatar['name'], PATHINFO_EXTENSION), $allowedMimeTypes)) {
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
            return Messages::VALIDATION_LEN_PASSWORD;
        }
        if ($request->password != $request->password_again) {
            return Messages::VALIDATION_PASSWORD_AGAIN;
        }
        //lowercase,uppercase,special symbol,number
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@$%^&*]).*$/', $request->password)) {
            return Messages::VALIDATION_PASSWORD_REGEX;
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
        return preg_match('/^(?:[A-Za-z0-9+\/]{4})*(?:[A-Za-z0-9+\/]{2}==|[A-Za-z0-9+\/]{3}=)?$/', $str);
    }

    /**
     * Performs common validation checks for related requests.
     *
     * This method consolidates common validation checks including CAPTCHA validation and CSRF token validation.
     *
     * @param Request $request containing the data to be validated.
     *
     * @return string|null Returns a validation error message if any of the checks fail, or `null` if all checks pass.
     *
     */
    private function commonValidation(Request $request): ?string
    {
        if (!is_null($this->validateCaptcha($request->grecaptcharesponse))) {
            return $this->validateCaptcha($request->grecaptcharesponse);
        }
        if (!$this->validToken($request->token)) {
            return Messages::VALIDATION_CSRF_ERROR;
        }
        return null;
    }

    /**
     * Validates CAPTCHA response with Google's reCAPTCHA API.
     *
     * @param ?string $response The CAPTCHA response from the user.
     *
     * @return string|null Returns an error message if CAPTCHA validation fails, or null if validation passes.
     */
    private function validateCaptcha(?string $response): ?string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(
            [
                'secret' => $_ENV['RECAPTCHA_PRIVATE'],
                'response' => $response
            ]
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($response, true);

        if (isset($res['error-codes'])) {
            foreach ($res['error-codes'] as $msg) {
                return 'danger_' . $msg;
            }
        }
        return null;
    }

    /**
     * Validates CSRF token.
     *
     * @param string $token The CSRF token to validate.
     *
     * @return bool Returns true if the token is valid, otherwise false.
     */
    private function validToken(string $token): bool
    {
        if (strcmp($this->encryption->decrypt($token), $_ENV['CSRFKEY']) === 0) {
            return true;
        }
        return false;
    }
}
