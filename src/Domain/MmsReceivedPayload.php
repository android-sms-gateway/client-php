<?php

namespace AndroidSmsGateway\Domain;

/**
 * Payload of an mms:received event (MMS notification, not yet downloaded).
 */
class MmsReceivedPayload {
    private string $messageId;
    private string $phoneNumber;
    private string $sender;
    private string $transactionId;
    private string $contentClass;
    private int $size;
    private string $receivedAt;
    private ?string $recipient;
    private ?int $simNumber;
    private ?string $subject;

    public function __construct(
        string $messageId,
        string $phoneNumber,
        string $sender,
        string $transactionId,
        string $contentClass,
        int $size,
        string $receivedAt,
        ?string $recipient = null,
        ?int $simNumber = null,
        ?string $subject = null
    ) {
        $this->messageId = $messageId;
        $this->phoneNumber = $phoneNumber;
        $this->sender = $sender;
        $this->transactionId = $transactionId;
        $this->contentClass = $contentClass;
        $this->size = $size;
        $this->receivedAt = $receivedAt;
        $this->recipient = $recipient;
        $this->simNumber = $simNumber;
        $this->subject = $subject;
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
            $obj->transactionId,
            $obj->contentClass,
            $obj->size,
            $obj->receivedAt,
            $obj->recipient ?? null,
            $obj->simNumber ?? null,
            $obj->subject ?? null
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

    public function TransactionId(): string {
        return $this->transactionId;
    }

    public function ContentClass(): string {
        return $this->contentClass;
    }

    public function Size(): int {
        return $this->size;
    }

    public function ReceivedAt(): string {
        return $this->receivedAt;
    }

    public function Recipient(): ?string {
        return $this->recipient;
    }

    public function SimNumber(): ?int {
        return $this->simNumber;
    }

    public function Subject(): ?string {
        return $this->subject;
    }
}
