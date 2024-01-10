<?php

namespace AndroidSmsGateway\Domain;

use AndroidSmsGateway\Encryptor;
use AndroidSmsGateway\Interfaces\SerializableInterface;

/**
 * Message
 */
class Message implements SerializableInterface {
    /**
     * Message ID, will be generated automatically if not set
     */
    private ?string $id;
    /**
     * Message text
     * Long message will be divided into parts
     */
    private string $message;
    /**
     * Time to live in seconds
     * If message is not received by device in this time, it will be failed
     */
    private ?int $ttl;
    /**
     * Sim card number, if not set, will be used default
     */
    private ?int $simNumber;
    /**
     * Request delivery report, `true` by default
     */
    private bool $withDeliveryReport;
    /**
     * Is message and phones encrypted, `false` by default
     */
    private bool $isEncrypted = false;
    /**
     * Phone numbers in E164 format
     * @var array<string>
     */
    private array $phoneNumbers;

    /**
     * @param array<string> $phoneNumbers
     */
    public function __construct(
        string $message,
        array $phoneNumbers,
        ?string $id = null,
        ?int $ttl = null,
        ?int $simNumber = null,
        bool $withDeliveryReport = true
    ) {
        $this->id = $id;
        $this->message = $message;
        $this->ttl = $ttl;
        $this->simNumber = $simNumber;
        $this->withDeliveryReport = $withDeliveryReport;
        $this->phoneNumbers = $phoneNumbers;
        $this->isEncrypted = false;
    }

    public function Encrypt(Encryptor $encryptor): self {
        if ($this->isEncrypted) {
            return $this;
        }

        $this->isEncrypted = true;
        $this->message = $encryptor->Encrypt($this->message);
        $this->phoneNumbers = array_map(
            fn(string $phoneNumber) => $encryptor->Encrypt($phoneNumber),
            $this->phoneNumbers
        );
        return $this;
    }

    public function ToObject(): object {
        return (object) [
            'id' => $this->id,
            'message' => $this->message,
            'ttl' => $this->ttl,
            'simNumber' => $this->simNumber,
            'withDeliveryReport' => $this->withDeliveryReport,
            'isEncrypted' => $this->isEncrypted,
            'phoneNumbers' => $this->phoneNumbers
        ];
    }
}