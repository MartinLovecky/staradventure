<?php
if(isset($_POST['submit'])){
    match($_POST['type']){
        'register'      => $requestController->submitRegister(),
        'login'         => $requestController->submitLogin(),
        'reset_send'    => $requestController->submitResetSend(),
        'reset_user'    => $requestController->submitForgottenUser(),
        'new_password'  => $requestController->setNewPassword(),
        'bookmark'      => $requestController->submitBookmark(),
        'kontakt'       => $requestController->submitKontakt(),
        'update_member' => $requestController->updateMember(),
        'update'        => $articleController->update(),
        'delete'        => $articleController->delete(),
        'create'        => $articleController->create(),
        default => null,
    };
}
?>