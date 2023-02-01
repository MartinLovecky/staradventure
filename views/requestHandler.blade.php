<?php

$request->getRequest();

if(isset($request->type))
{
    match($request->type){
<<<<<<< HEAD
        'register'      => $memberController->submitRegister($request),
        'login'         => $memberController->submitLogin($request),
        'reset_send'    => $memberController->submitResetSend($request),
        'reset_user'    => $memberController->submitForgottenUser($request),
        'new_password'  => $memberController->setNewPassword($request),
        'bookmark'      => $memberController->submitBookmark($request),
        'kontakt'       => $memberController->submitKontakt($request),
=======
        'register'      => $memberController->register($request),
        'login'         => $memberController->login($request),
        'reset_send'    => $memberController->sendResetToken($request),
        'reset_user'    => $memberController->sendForgottenUser($request),
        'new_password'  => $memberController->setNewPassword($request),
>>>>>>> 79c63082bcf0d2c62485e62b96d9f6bbb854e1cc
        'update_member' => $memberController->updateMember($request),
        'update'        => $articleController->update($request),
        'delete'        => $articleController->delete($request),
        'create'        => $articleController->create($request),
        default => null,
    };
}  
?>