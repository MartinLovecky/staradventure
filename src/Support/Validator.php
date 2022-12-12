<?php

namespace Mlkali\Sa\Support;

use Mlkali\Sa\Database\Entity\Member;
use Mlkali\Sa\Http\Request;
use Mlkali\Sa\Support\Encryption;


class Validator{

    public function __construct(
        private Encryption $enc,
        private Member $member
    ) 
    {
    }

    public function validateRegister(Request $request): ?string
    {
        if ($request->vops !== 'on' && $request->terms !== 'on') {
            return 'danger.checkbox failed';
        }
        $recaptcha = $this->validateCaptcha($request->grecaptcharesponse);
        if (isset($recaptcha)) {
            return $recaptcha;
        }
        if (!$this->validToken($request->token)) {
            return 'danger.Csfr validation failed';
        }
        if (!$this->member->getMemberInfo('member_id', $request->username.'|'.$request->email)) {
            return 'danger.Member ' . $request->username . ' alredy exists';
        }
        if (mb_strlen($request->password) < 6) {
            return 'danger.Heslo musí obsahovat nejméně 6 znaků';
        }
        if ($request->password != $request->password_again) {
            return 'danger.Hesla se musí schodovat';
        }
        //lowercase,uppercase,special symbol,number
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@$%^&*]).*$/', $request->password)) {
            return 'danger.Heslo musí obasahovat nejméně jedno malé a velké písmeno a jeden specialní znak(!@$%^&)';
        }
        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            return 'danger.Nesprávný formát emailu (' . $request->email . ')';
        }
        if (mb_strlen($request->username) < 4) {
            return 'danger.Username (' . $request->username . ') musí mít nejméně 4 znaky';
        }
        return null;
    }
 /*   
    public function validateLogin(Request $request): ?string
    {
        $recaptcha = $this->validateCaptcha($request->grecaptcharesponse);

        if(isset($recaptcha)){
            return $recaptcha;
        }

        if($this->validToken($request->token) === false){
            return 'danger.Csfr validation failed';
        }
        
        if($this->member->getMemberInfo($request->username)){
            return 'danger.Uživatel '.$request->username.' neexistuje';
        }
        return null;
    }
    
   
    

    public function validateResetSend(Request $request): ?string
    {
        $recaptcha = $this->validateCaptcha($request->grecaptcharesponse);

        if(isset($recaptcha)){
            return $recaptcha;
        }
        if(!filter_var($request->email, FILTER_VALIDATE_EMAIL)){
            return 'danger.Nesprávný formát emailu';
        }
        if(!$this->member->isUnique($request->email)){
            return 'danger.Email neexistuje v database';
        }
        if($this->member->resetCompleted($request->email) === false){
            return 'danger.Zkontrolujte si svůj email';
        }
        return null;
    }

    public function validatePassword(Request $request): ?string
    {
        $recaptcha = $this->validateCaptcha($request->grecaptcharesponse);

        if(isset($recaptcha)){
            return $recaptcha;
        }
        if(mb_strlen($request->password) < 6){
            return 'danger.Heslo musí obsahovat nejméně 6 znaků';
        }
        if($request->password != $request->password_again){
            return 'danger.Hesla se musí schodovat';
        }
        //lowercase,uppercase,special symbol,number
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@$%^&]).*$/', $request->password)) {
            return 'danger.Heslo musí obasahovat nejméně jedno malé a velké písmeno a jeden specialní znak(!@$%^&)';
        }
        return null;
    }
    
    //TODO 
    public function validateForgottenUser(Request $request){
        return null;
    }
    
    public function validateAvatar($filePath,$fileType,$fileSize): ?string
    {
        if(!is_uploaded_file($filePath)){
            return 'danger.Avatar musí být nahrán přes formulář';
        }
        if(!isset($filePath)){
            return 'danger.Avatar musí být nahrán';
        }
        if(filesize($fileSize) === 0){
            return 'danger.Avatar musí být nahrán';
        }
        if(filesize($fileSize) > 5145728){
            return 'danger.Avatar nesmí mít více jak 5MB';
        }
        if(!in_array(
            finfo_file(finfo_open(FILEINFO_MIME_TYPE),mime_content_type($fileType)),
            array_keys(['image/png' => 'png','image/jpg' => 'jpg', 'image/jpeg' => 'jpeg']))
        ){
            return 'danger.File musí být png nebo jpg';
        }
        return null;
    }
*/
    private function validateCaptcha(?string $response): ?string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['secret' => $_ENV['RECAPTCHA'], 'response' => $response]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($response, true);

        if (!$res['success']) {
            $err =  $res['error-codes'];
            foreach ($err as $msg) {
                return 'danger.' . $msg;
            }
        }
        return null;
    }

    private function validToken(string $token): bool
    {
        $decrypted = $this->enc->decrypt($token);

        if (strcmp($decrypted, $_ENV['CSRFKEY']) === 0) {
            return true;
        }
        return false;
    }
}
