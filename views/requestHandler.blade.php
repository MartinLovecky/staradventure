<?php
$request->getRequest();

if(isset($request->type))
{
    match($request->type){
        'register'      => $requestController->submitRegister($request),
        'login'         => $requestController->submitLogin($request),
        'reset_send'    => $requestController->submitResetSend($request),
        'reset_user'    => $requestController->submitForgottenUser($request),
        'new_password'  => $requestController->setNewPassword($request),
        'bookmark'      => $requestController->submitBookmark($request),
        'kontakt'       => $requestController->submitKontakt($request),
        'update_member' => $requestController->updateMember($request),
        'update'        => $articleController->update($request),
        'delete'        => $articleController->delete($request),
        'create'        => $articleController->create($request),
        default => null,
    };
}  
?>