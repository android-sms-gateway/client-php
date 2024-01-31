<?php

use AndroidSmsGateway\Encryptor;
use PHPUnit\Framework\TestCase;

class EncryptorTest extends TestCase {
    public function testEncryptDecrypt(): void {
        $passphrase = 'MySecretPassphrase';
        $encryptor = new Encryptor($passphrase);

        $originalData = 'Sensitive data here';
        $encryptedData = $encryptor->Encrypt($originalData);
        $decryptedData = $encryptor->Decrypt($encryptedData);

        $this->assertEquals($originalData, $decryptedData, 'Decrypted data should match original data.');
    }

    public function testDecryptWithWrongPassphrase(): void {
        $this->expectException(\RuntimeException::class);

        $passphrase = 'MySecretPassphrase';
        $wrongPassphrase = 'WrongPassphrase';
        $encryptor = new Encryptor($passphrase);
        $wrongEncryptor = new Encryptor($wrongPassphrase);

        $originalData = 'Sensitive data here';
        $encryptedData = $encryptor->Encrypt($originalData);

        // Try to decrypt with wrong passphrase
        $wrongEncryptor->Decrypt($encryptedData);
    }

    public function testDecryptWithUnsupportedAlgorithm(): void {
        $this->expectException(\RuntimeException::class);

        $passphrase = 'MySecretPassphrase';
        $encryptor = new Encryptor($passphrase);

        // Manually construct data with unsupported algorithm
        $unsupportedData = '$unsupported-algo$i=10000$fakeSalt$fakeEncryptedData';

        $encryptor->Decrypt($unsupportedData);
    }

    public function testDecryptWithMissingIterationCount(): void {
        $this->expectException(\RuntimeException::class);

        $passphrase = 'MySecretPassphrase';
        $encryptor = new Encryptor($passphrase);

        // Manually construct data with missing iteration count
        $missingIterationCountData = '$aes-256-cbc/pbkdf2-sha1$x=1$fakeSalt$fakeEncryptedData';

        $encryptor->Decrypt($missingIterationCountData);
    }
}
