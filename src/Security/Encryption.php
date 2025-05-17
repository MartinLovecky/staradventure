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
        [$version, $key] = $this->getLatestKey();

        $nonce = random_bytes(SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES);
        $ciphertext = sodium_crypto_aead_xchacha20poly1305_ietf_encrypt(
            $message,
            $aad,
            $nonce,
            $key
        );

        return $version . ':' . bin2hex($nonce . $ciphertext);
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

        [$version, $hex] = explode(':', $ciphertext, 2);

        if (!isset($_ENV["EKEY_$version"])) {
            throw new Exception("Encryption key for version $version not found.");
        }

        $key = $this->decodeKey($_ENV["EKEY_$version"]);

        $decoded = hex2bin($hex);
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
            $key
        );

        if ($decrypted === false) {
            throw new Exception('Decryption failed');
        }

        return $decrypted;
    }

    public function isEncrypted(string $input): bool
    {
        if (empty($input) || strpos($input, ':') === false) {
            return false;
        }

        [$version, $hex] = explode(':', $input, 2);

        if (!isset($_ENV["EKEY_{$version}"])) {
            return false;
        }

        if (!ctype_xdigit($hex)) {
            return false;
        }

        if (!$decoded = hex2bin($hex)) {
            return false;
        }

        return mb_strlen($decoded, '8bit') > SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES;
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
        $version = 'v' . date('YmdHis');
        $key = base64_encode(sodium_crypto_aead_xchacha20poly1305_ietf_keygen());

        return $version . ':' . $key;
    }

    private function getLatestKey(): array
    {
        $keys = array_filter($_ENV, fn ($k) => str_starts_with($k, 'EKEY_'), ARRAY_FILTER_USE_KEY);

        if (empty($keys)) {
            throw new Exception('No encryption key found');
        }

        $versions = array_map(fn ($k) => str_replace('EKEY_', '', $k), array_keys($keys));
        rsort($versions, SORT_NATURAL);

        $latestVersion = $versions[0];
        $key = $this->decodeKey($_ENV["EKEY_$latestVersion"]);

        return [$latestVersion, $key];
    }

    /**
     * Decode and validate a key from an env string like "v20250501035806:base64key"
     *
     * @param string $envKey
     * @return string
     */
    private function decodeKey(string $envKey): string
    {
        [$ver, $b64key] = explode(':', $envKey, 2);
        $key = base64_decode($b64key, true);

        if ($key === false || strlen($key) !== SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_KEYBYTES) {
            throw new Exception("Invalid encryption key format or length.");
        }

        return $key;
    }
}
