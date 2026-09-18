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
     * MMS message content (mutually exclusive with text/data messages)
     * @var MmsMessage|null
     */
    private ?MmsMessage $mmsMessage;

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
     * Message priority
     */
    private ?int $priority = null;
    /**
     * Valid until timestamp
     */
    private ?string $validUntil;

    /**
     * @param string|MmsMessage $message
     * @param array<string> $phoneNumbers
     */
    public function __construct(
        $message,
        array $phoneNumbers,
        ?string $id = null,
        ?int $ttl = null,
        ?int $simNumber = null,
        bool $withDeliveryReport = true,
        ?int $priority = null,
        ?string $validUntil = null
    ) {
        if ($ttl !== null && $validUntil !== null) {
            throw new \InvalidArgumentException('validUntil and ttl cannot be set at the same time');
        }

        $this->id = $id;
        $this->ttl = $ttl;
        $this->simNumber = $simNumber;
        $this->withDeliveryReport = $withDeliveryReport;
        $this->phoneNumbers = $phoneNumbers;
        $this->isEncrypted = false;
        $this->priority = $priority;
        $this->validUntil = $validUntil;

        if ($message instanceof MmsMessage) {
            $this->mmsMessage = $message;
            $this->message = '';
        } else {
            $this->message = $message;
            $this->mmsMessage = null;
        }
    }


    /**
     * Get MMS message content (null if not set)
     * @return MmsMessage|null
     */
    public function MmsMessage(): ?MmsMessage {
        return $this->mmsMessage;
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

        if ($this->mmsMessage !== null) {
            $this->mmsMessage = new MmsMessage(
                $this->mmsMessage->Subject() !== null
                ? $encryptor->Encrypt($this->mmsMessage->Subject())
                : null,
                $this->mmsMessage->Text() !== null
                ? $encryptor->Encrypt($this->mmsMessage->Text())
                : null,
                array_map(
                    fn(MmsAttachment $attachment) => new MmsAttachment(
                        $attachment->ContentType(),
                        $encryptor->Encrypt($attachment->Data()),
                        $attachment->Name() !== null
                        ? $encryptor->Encrypt($attachment->Name())
                        : null
                    ),
                    $this->mmsMessage->Attachments()
                )
            );
        }

        return $this;
    }

    public function ToObject(): object {
        $obj = (object) [
            'id' => $this->id,
            'simNumber' => $this->simNumber,
            'withDeliveryReport' => $this->withDeliveryReport,
            'isEncrypted' => $this->isEncrypted,
            'phoneNumbers' => $this->phoneNumbers,
        ];

        if ($this->mmsMessage !== null) {
            $obj->mmsMessage = $this->mmsMessage->ToObject();
        } else {
            $obj->message = $this->message;
        }

        if ($this->priority !== null) {
            $obj->priority = $this->priority;
        }

        if ($this->ttl !== null) {
            $obj->ttl = $this->ttl;
        }

        if ($this->validUntil !== null) {
            $obj->validUntil = $this->validUntil;
        }

        return $obj;
    }
}