<?php

namespace Mlkali\Sa\Support;
/**
 * This encryption class should not be used for storing any data if you want make 
 * function encrypt() more secure $_ENV['ENON'] must be replaced by $this->generateNonce()
 * nonce needs to be stored (to be able decrypt) and must be updated every time request to page is made
 * aka refresh, post, redirected etc for me its not necessary bcs I dont encrypt important data with this Class
 * @author Martin Lovecky 
 */
class Encryption{

    /**
     * Sodium 8.1 Encryption
     * @param string $_ENV['ENON'] $this->generateNonce();
     * @param string $_ENV['EKEY'] $this->generateKey();
     * @return string static encrypted string
     */
    public function encrypt(string $message): string
    {
        return bin2hex(sodium_crypto_stream_xchacha20_xor($message, base64_decode($_ENV['ENON']), base64_decode($_ENV['EKEY'])));
    }

    public function decrypt(string $ciphertext): string
    {
        return sodium_crypto_stream_xchacha20_xor(hex2bin($ciphertext), base64_decode($_ENV['ENON']), base64_decode($_ENV['EKEY']));
    }

    public function generateKey(): string
    {
        return base64_encode(sodium_crypto_stream_xchacha20_keygen());
    }

    public function generateNonce(): string
    {
        return base64_encode(random_bytes(SODIUM_CRYPTO_STREAM_XCHACHA20_NONCEBYTES));
    }
}