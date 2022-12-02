<?php

namespace Mlkali\Sa\Support;

class Encryption{

    /**
     * Sodium 8.1 encrpytion
     *
     * @param string $_ENV['ENON'] = generateNonce()
     * @param string $_ENV['EKEY'] = generateKey()
     * @return string 
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