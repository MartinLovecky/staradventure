<?php

namespace Mlkali\Sa\Controllers;

use Mlkali\Sa\Database\DB;
use Mlkali\Sa\Http\Request;
use Mlkali\Sa\Http\Response;
use Mlkali\Sa\Support\Mailer;
use Mlkali\Sa\Support\Validator;
use Mlkali\Sa\Controllers\MemberController;

class RequestController{

    public function __construct(
        private Request $request, 
        private DB $db,
        private Mailer $mailer,
        private Validator $validator,
        private MemberController $memberController,
    )
    {   
    }
    
    public function submitRegister(): Response
    {
        $validate = $this->validator->validateRegister($this->request);

        if(isset($validate)){

            @$_SESSION = [
                'old_username' => $this->request->username, 
                'old_email' => $this->request->email
            ];
            
            return new Response('/register?message=',$validate,'#register');           
        }
      
        $this->memberController->register($this->request);

        return new Response('/login?message=','success.Byl vám odeslán aktivační email (zkontrolujte prosím i spam)','#login');
    }

    public function submitLogin(): Response
    {
        $validate = $this->validator->validateLogin($this->request);

        if(isset($validate)){

            @$_SESSION = [
                'old_username' => $this->request->username,
            ];

            return new Response('/login?message=',$validate,'#login');
        }
        
        if(isset($this->request->remember)){
            
            setcookie('remember' , $this->request->username, time() + (86400 * 7), '/');
            
            return new Response('member/'.$this->request->username.'?message=','success.Vítejte zpět '.$this->request->username);
        }
        else
        {
            $memberID = $this->member->getMemberID($this->request->username);
            $this->member->setMemberData($memberID);
            return new Response('member/'.$this->request->username.'?message=','success.Vítejte zpět '.$this->request->username);
        }
    }

    public function submitResetSend(): Response
    {
        $validate = $this->validator->validateResetSend($this->request);

        if(isset($validate)){

            @$_SESSION = [
                'old_email' => $this->request->email
            ];

            return new Response('/?message=',$validate,'#reset');
        }

        $token = md5(uniqid(rand(), true));

        $body = str_replace(
            ['YourUsername', 'TOKEN', 'URL', 'ACHASH'], 
            [$this->request->email, base64_encode($this->request->email), $_SERVER['HTTP_ORIGIN'], $token], 
            file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/public/template/reset.php')
        );

        $info = ['subject'=>'Reset hesla','to'=>$this->request->email];

        if($this->mailer->sender($body, $info)){

            return new Response('/?message=','success.Odkaz na změnu hesla byl odeslán na email','#');
        }
    }
        
    public function setNewPassword(): Response
    {
        $validate = $this->validator->validatePassword($this->request);

        if(isset($validate)){

            return new Response('/?message=',$validate,'#newpassword');    
        }

        $hash = password_hash($this->request->password, PASSWORD_BCRYPT);
        
        $this->db->query
                ->update('members')
                ->set(['password' => $hash])
                ->where('email', $this->request->email)
                ->execute();
                
        return new Response('/?message=','success.Heslo bylo úspěšně změněno','#login');           
    }
    
    public function submitForgottenUser() : Response
    {
        $validate = $this->validator->validateForgottenUser($this->request);

        if(isset($validate)){

            return new Response('/reset?message=','danger.Neplatný email ('.$this->request->email.')','#reset');
        }

        $stmt = $this->db->query
                ->from('members')
                ->select('username')
                ->where('email', $this->request->email);
        
        $username = $stmt->fetch('username');

        $body = str_replace(
                ['YourUsername', 'URL'], 
                [$username, $_SERVER['HTTP_ORIGIN']], 
                file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/public/template/username.php')
        );

        $info = ['subject' => 'Zapomenutné Username', 'to' => $this->request->email];

        $this->mailer->sender($body, $info);

        return new Response('/login?message=','succes.Uživatelské jméno bylo zaslíno na váš email','#login');
    }
    
    public function updateMember(): Response
    {
        $filePath = $_FILES['avatar']['tmp_name'];
        $fileType = mime_content_type($filePath);
        $fileName = basename($filePath);
        $fileSize = filesize($filePath);
        
        $validate = $this->validator->validateAvatar($filePath,$fileType,$fileSize);

        if(isset($validate)){
            return new Response('/reset?message=',$validate,'#updatemember');
        }

        $allowedTypes = [
            'image/png' => 'png',
            'image/jpeg' => 'jpeg',
            'image/jpg' => 'jpg'
        ];

        $extension = $allowedTypes[$fileType];
        $targetDir = $_SERVER['DOCUMENT_ROOT'] . '/public/img/avatars/';

        $newFilePath = $targetDir.$fileName.'.'.$extension;

        move_uploaded_file($filePath, $newFilePath);
        unlink($filePath);

        //TODO - Update db table

        return new Response('/member'.$this->request->username.'?message=','succes.Informace upraveny');
    }
}