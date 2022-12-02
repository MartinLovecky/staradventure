<?php

//REVIEW - Hard to implement having multiple issues with sodium
//NOTE - Currently not used
/*
namespace Mlkali\Sa\Support;

use ParagonIE\Halite\KeyFactory;
use ParagonIE\Halite\Alerts\HaliteAlert;
use ParagonIE\HiddenString\HiddenString;
use ParagonIE\Halite\Symmetric\EncryptionKey;
use ParagonIE\Halite\Symmetric\Crypto as Symmetric;

class Encryption{
 
    public function encrypt(string $message)
    {
        try {
           return  $this->_getKey();
                       //code...
        } catch (HaliteAlert $e) {
            throw $e;
        }
    }
    
    public function decrypt(string $cipherText): HiddenString
    {
        return Symmetric::decrypt($cipherText, $this->_getKey());
    }
 
    public function key(): string
    {
        $enc_key = KeyFactory::generateEncryptionKey();

        return KeyFactory::export($enc_key)->getString();   
    }

    private function _getKey(): EncryptionKey
    {
        return KeyFactory::loadEncryptionKey('/var/www/encryption.key');
    }
}
*/
