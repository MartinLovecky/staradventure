<?php

namespace Mlkali\Sa\Support;

use Exception;
/**
 * @method string encrypt(string $message, $aad = '')
 * @method string decrypt(string $ciphertext, $aad = '')
 * @method string generateKey()
 */
class Encryption
{

    public function encrypt(string $message, $aad = ''): string
    {
        $nonce = random_bytes(SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES);
        $ciphertext = sodium_crypto_aead_xchacha20poly1305_ietf_encrypt($message, $aad, $nonce, base64_decode($_ENV['EKEY']));

        return bin2hex($nonce . $ciphertext);
    }
    
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

    public function generateKey(): string
    {
        return base64_encode(sodium_crypto_aead_xchacha20poly1305_ietf_keygen());
    }
}
