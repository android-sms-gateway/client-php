<?php

namespace AndroidSmsGateway\Domain;

/**
 * Payload of an sms:data-received event (base64-encoded content).
 */
final class SmsDataReceivedPayload {
    private string $messageId;
    private string $phoneNumber;
    private string $sender;
    private ?string $recipient;
    private ?int $simNumber;
    private string $data;
    private string $receivedAt;

    public function __construct(
        string $messageId,
        string $phoneNumber,
        string $sender,
        ?string $recipient,
        ?int $simNumber,
        string $data,
        string $receivedAt
    ) {
        $this->messageId = $messageId;
        $this->phoneNumber = $phoneNumber;
        $this->sender = $sender;
        $this->recipient = $recipient;
        $this->simNumber = $simNumber;
        $this->data = $data;
        $this->receivedAt = $receivedAt;
    }

    /**
     * @param object $obj
     * @return self
     */
    public static function FromObject(object $obj): self {
        return new self(
            $obj->messageId,
            $obj->phoneNumber,
            $obj->sender,
            $obj->recipient ?? null,
            $obj->simNumber ?? null,
            $obj->data,
            $obj->receivedAt
        );
    }

    public function MessageId(): string {
        return $this->messageId;
    }

    public function PhoneNumber(): string {
        return $this->phoneNumber;
    }

    public function Sender(): string {
        return $this->sender;
    }

    public function Recipient(): ?string {
        return $this->recipient;
    }

    public function SimNumber(): ?int {
        return $this->simNumber;
    }

    public function Data(): string {
        return $this->data;
    }

    public function ReceivedAt(): string {
        return $this->receivedAt;
    }
}
