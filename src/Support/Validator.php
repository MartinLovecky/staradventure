<?php

namespace Mlkali\Sa\Support;

use Mlkali\Sa\Http\Request;
use Mlkali\Sa\Support\Messages;
use Mlkali\Sa\Support\Encryption;
use Mlkali\Sa\Database\Repository\MemberRepository;

class Validator
{

    public function __construct(
        private Encryption $enc,
        private MemberRepository $memRepo
    ) {
    }

    public function validateRegister(Request $request): ?string
    {
        if ($request->vops !== 'on' && $request->terms !== 'on') {
            return Messages::VALIDATION_REG_CHECKBOX_FAIL;
        }
        if (!is_null($this->validateCaptcha($request->grecaptcharesponse))) {
            return $this->validateCaptcha($request->grecaptcharesponse);
        }
        if (!$this->validToken($request->token)) {
            return Messages::VALIDATION_CRSF_ERROR;
        }
        if ($this->memRepo->getMemberInfo('member_id', $request->username . '|' . $request->email)) {
            return sprintf(Messages::VALIDATION_USER_ALREADY_EXISTS, $request->username);
        }
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
        // email validation structure
        if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $request->email)) {
            return sprintf(Messages::VALIDATION_EMAIL_FORMAT, $request->email);
        }
        if (mb_strlen($request->username) < 4) {
            return sprintf(Messages::VALIDATION_LEN_USER, $request->username);
        }
        return null;
    }

    public function validateLogin(Request $request, string $activeMember): ?string
    {
        if (!is_null($this->validateCaptcha($request->grecaptcharesponse))) {
            return $this->validateCaptcha($request->grecaptcharesponse);
        }
        if (strcmp($activeMember, 'yes') !== 0) {
            return Messages::VALIDATION_ACTIVE_MEMBER;
        }
        if (!$this->validToken($request->token)) {
            return Messages::VALIDATION_CRSF_ERROR;
        }
        if (!$this->memRepo->getMemberInfo('username', $request->username, 'username')) {
            return sprintf(Messages::VALIDATION_USER_NOT_EXIST, $request->username);
        }
        return null;
    }

    public function validateResetSend(Request $request): ?string
    {
        if (!is_null($this->validateCaptcha($request->grecaptcharesponse))) {
            return $this->validateCaptcha($request->grecaptcharesponse);
        }
        if (!$this->validToken($request->token)) {
            return Messages::VALIDATION_CRSF_ERROR;
        }
        if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $request->email)) {
            return sprintf(Messages::VALIDATION_EMAIL_FORMAT, $request->email);
        }
        if (!$this->memRepo->getMemberInfo('email', $request->email, 'email')) {
            return sprintf(Messages::VALIDATION_USER_NOT_EXIST, $request->email);
        }
        return null;
    }

    public function validatePassword(Request $request): ?string
    {
        if (!is_null($this->validateCaptcha($request->grecaptcharesponse))) {
            return $this->validateCaptcha($request->grecaptcharesponse);
        }
        if (!$this->validToken($request->token)) {
            return Messages::VALIDATION_CRSF_ERROR;
        }
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
    //TODO - need to test 
    public function validateAvatar(Request $request): ?string
    {
        return dd(NULL);    
        if (!is_null($this->validateCaptcha($request->grecaptcharesponse))) {
            return $this->validateCaptcha($request->grecaptcharesponse);
        }
        if (!$this->validToken($request->token)) {
            return Messages::VALIDATION_CRSF_ERROR;
        }
        if (!is_uploaded_file($request->avatar['tmp_name'])) {
            return Messages::AVATAR_UPLOAD;
        }
        if (!isset($request->avatar['name'])) {
            return Messages::AVATAR_UPLOAD;
        }
        if (filesize($request->avatar['size']) === 0) {
            return Messages::AVATAR_UPLOAD;
        }
        if (filesize($request->avatar['size']) > 5145728) {
            return Messages::AVATAR_SIZE;
        }
        if (!in_array(
            finfo_file(finfo_open(FILEINFO_MIME_TYPE), mime_content_type($request->avatar['type'])),
            array_keys(['image/png' => 'png', 'image/jpg' => 'jpg', 'image/jpeg' => 'jpeg'])
        )) {
            return Messages::AVATAR_MIME_TYPE;
        }
        return null;
    }

    private function validateCaptcha(?string $response): ?string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(
            [
                'secret' => $_ENV['RECAPTCHA'],
                'response' => $response
            ]
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($response, true);

        if (!$res['success']) {
            foreach ($res['error-codes'] as $msg) {
                return 'danger_' . $msg;
            }
        }
        return null;
    }

    private function validToken(string $token): bool
    {
        if (strcmp($this->enc->decrypt($token), $_ENV['CSRFKEY']) === 0) {
            return true;
        }
        return false;
    }
}
