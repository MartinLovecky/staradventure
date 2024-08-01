<?php

namespace Mlkali\Sa\Support;

use Exception;

class Encryption
{
    /**
     * Method encrypt
     *
     * @param string $message [explicite description]
     * @param $aad $aad [explicite description]
     *
     * @return string
     */
    public function encrypt(string $message, $aad = ''): string
    {
        $nonce = random_bytes(SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES);
        $ciphertext = sodium_crypto_aead_xchacha20poly1305_ietf_encrypt($message, $aad, $nonce, base64_decode($_ENV['EKEY']));

        return bin2hex($nonce . $ciphertext);
    }

    /**
     * Method decrypt
     *
     * @param string $ciphertext [explicite description]
     * @param $aad $aad [explicite description]
     *
     * @return string
     */
    public function decrypt(string $ciphertext, $aad = ''): string
    {
        if (empty($ciphertext)) {
            return '';
        }

        $decoded = hex2bin($ciphertext);

        if ($decoded === false) {
            throw new Exception('Invalid data format');
        }
        if (mb_strlen($decoded, '8bit') < SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES) {
            throw new Exception('Invalid data length');
        }
        $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES, '8bit');
        $data = mb_substr($decoded, SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES, null, '8bit');

        $decrypted = sodium_crypto_aead_xchacha20poly1305_ietf_decrypt($data, $aad, $nonce, base64_decode($_ENV['EKEY']));

        if ($decrypted === false) {
            throw new Exception('Decryption failed');
        }

        return $decrypted;
    }

    /**
     * Method token
     *
     * @return string
     */
    public function token(): string
    {
        // hash('sha256', random_bytes(32)); if needed
        return bin2hex(random_bytes(32));
    }

    /**
     * Method generateKey
     *
     * @return string
     */
    public function generateKey(): string
    {
        return base64_encode(sodium_crypto_aead_xchacha20poly1305_ietf_keygen());
    }
}
