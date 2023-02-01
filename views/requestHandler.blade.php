<?php

$request->getRequest();

if(isset($request->type))
{
    match($request->type){
        'register'      => $memberController->submitRegister($request),
        'login'         => $memberController->submitLogin($request),
        'reset_send'    => $memberController->submitResetSend($request),
        'reset_user'    => $memberController->submitForgottenUser($request),
        'new_password'  => $memberController->setNewPassword($request),
        'bookmark'      => $memberController->submitBookmark($request),
        'kontakt'       => $memberController->submitKontakt($request),
        'update_member' => $memberController->updateMember($request),
        'update'        => $articleController->update($request),
        'delete'        => $articleController->delete($request),
        'create'        => $articleController->create($request),
        default => null,
    };
}  
?>