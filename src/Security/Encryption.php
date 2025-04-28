<?php

declare(strict_types=1);

namespace Mlkali\Sa\Security;

use Exception;

class Encryption
{
    /**
     * encrypt message with sodium
     *
     * @param string $message
     * @param string $aad
     *
     * @return string
     */
    public function encrypt(string $message = '', string $aad = ''): string
    {
        $nonce = random_bytes(SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES);
        $ciphertext = sodium_crypto_aead_xchacha20poly1305_ietf_encrypt(
            $message,
            $aad,
            $nonce,
            base64_decode($_ENV['EKEY'])
        );

        return bin2hex($nonce . $ciphertext);
    }

    /**
     * decrypt message
     *
     * @param string $ciphertext
     * @param string $aad
     *
     * @return string
     */
    public function decrypt(string $ciphertext = '', string $aad = ''): string
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

        $decrypted = sodium_crypto_aead_xchacha20poly1305_ietf_decrypt(
            $data,
            $aad,
            $nonce,
            base64_decode($_ENV['EKEY'])
        );

        if ($decrypted === false) {
            throw new Exception('Decryption failed');
        }

        return $decrypted;
    }

    public function encode(string $text): string
    {
        return password_hash($text, PASSWORD_BCRYPT);
    }

    public function generateCSRF(): string
    {
        $parts = explode('|', $_ENV['CSRFKEY']);
        return $this->encrypt($_ENV['CSRFKEY'], $parts[1]);
    }

    /**
     * token can be used for reset password
     *
     * @return string
     */
    public function token(): string
    {
        // hash('sha256', random_bytes(32)); if needed
        return bin2hex(random_bytes(32));
    }

    /**
     * generateKey for encryption
     *
     * @return string
     */
    public function generateKey(): string
    {
        return base64_encode(sodium_crypto_aead_xchacha20poly1305_ietf_keygen());
    }
}
