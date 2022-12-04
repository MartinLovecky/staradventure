<?php

return [
    'message' => $message, 
    'form' => $form,
    'selector' => $selector,
    'pagnition' => $pagnition,
    'member' => $member,
    'article' => $article,
    'enc' => $enc,
    'requestController' => $requestController,
    'articleController' => $articleController,
    'csrf' => $enc->encrypt($_ENV['CSRFKEY'])
];