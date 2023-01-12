<?php

$request->getRequest();

if(isset($request->type))
{
    match($request->type){
        'register'      => $memberController->register($request),
        'login'         => $memberController->login($request),
        'reset_send'    => $memberController->sendResetToken($request),
        'reset_user'    => $memberController->sendForgottenUser($request),
        'new_password'  => $memberController->setNewPassword($request),
        'update_member' => $memberController->updateMember($request),
        'update'        => $articleController->update($request),
        'delete'        => $articleController->delete($request),
        'create'        => $articleController->create($request),
        default => null,
    };
}  
?>