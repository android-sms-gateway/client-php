<?php

namespace AndroidSmsGateway;

class Encryptor {
    protected string $passphrase;
    protected int $iterationCount;

    /**
     * Encryptor constructor.
     * @param string $passphrase Passphrase to use for encryption
     * @param int $iterationCount Iteration count
     */
    public function __construct(
        string $passphrase,
        int $iterationCount = 75000
    ) {
        $this->passphrase = $passphrase;
        $this->iterationCount = $iterationCount;
    }

    public function Encrypt(string $data): string {
        $salt = $this->generateSalt();
        $secretKey = $this->generateSecretKeyFromPassphrase($this->passphrase, $salt, 32, $this->iterationCount);

        return sprintf(
            '$aes-256-cbc/pbkdf2-sha1$i=%d$%s$%s',
            $this->iterationCount,
            base64_encode($salt),
            openssl_encrypt($data, 'aes-256-cbc', $secretKey, 0, $salt)
        );
    }

    public function Decrypt(string $data): string {
        list($_, $algo, $paramsStr, $saltBase64, $encryptedBase64) = explode('$', $data);

        if ($algo !== 'aes-256-cbc/pbkdf2-sha1') {
            throw new \RuntimeException('Unsupported algorithm');
        }

        $params = $this->parseParams($paramsStr);
        if (empty($params['i'])) {
            throw new \RuntimeException('Missing iteration count');
        }

        $salt = base64_decode($saltBase64);
        $secretKey = $this->generateSecretKeyFromPassphrase($this->passphrase, $salt, 32, intval($params['i']));

        $decrypted = openssl_decrypt($encryptedBase64, 'aes-256-cbc', $secretKey, 0, $salt);
        if ($decrypted === false) {
            throw new \RuntimeException('Decryption failed, invalid passphrase?');
        }

        return $decrypted;
    }

    protected function generateSalt(int $size = 16): string {
        return random_bytes($size);
    }

    protected function generateSecretKeyFromPassphrase(
        string $passphrase,
        string $salt,
        int $keyLength = 32,
        int $iterationCount = 75000
    ): string {
        return hash_pbkdf2('sha1', $passphrase, $salt, $iterationCount, $keyLength, true);
    }

    /**
     * @return array<string, string>
     */
    protected function parseParams(string $params): array {
        $keyValuePairs = explode(',', $params);
        $result = [];
        foreach ($keyValuePairs as $pair) {
            list($key, $value) = explode('=', $pair, 2);
            $result[$key] = $value;
        }
        return $result;
    }
}