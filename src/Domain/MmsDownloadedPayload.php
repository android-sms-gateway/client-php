<?php

namespace AndroidSmsGateway\Domain;

/**
 * Payload of an mms:downloaded event (fully downloaded MMS with attachments).
 */
class MmsDownloadedPayload {
    private string $messageId;
    private string $phoneNumber;
    private string $sender;
    /** @var MmsDownloadedAttachment[] */
    private array $attachments;
    private string $receivedAt;
    private ?string $recipient;
    private ?int $simNumber;
    private ?string $subject;
    private ?string $body;

    /**
     * @param string $messageId
     * @param string $phoneNumber
     * @param string $sender
     * @param MmsDownloadedAttachment[] $attachments
     * @param string $receivedAt
     * @param string|null $recipient
     * @param int|null $simNumber
     * @param string|null $subject
     * @param string|null $body
     */
    public function __construct(
        string $messageId,
        string $phoneNumber,
        string $sender,
        array $attachments,
        string $receivedAt,
        ?string $recipient = null,
        ?int $simNumber = null,
        ?string $subject = null,
        ?string $body = null
    ) {
        $this->messageId = $messageId;
        $this->phoneNumber = $phoneNumber;
        $this->sender = $sender;
        $this->attachments = $attachments;
        $this->receivedAt = $receivedAt;
        $this->recipient = $recipient;
        $this->simNumber = $simNumber;
        $this->subject = $subject;
        $this->body = $body;
    }

    /**
     * @param object $obj
     * @return self
     */
    public static function FromObject(object $obj): self {
        $attachments = [];
        if (isset($obj->attachments) && is_array($obj->attachments)) {
            $attachments = array_map(
                static fn($a) => MmsDownloadedAttachment::FromObject($a),
                $obj->attachments
            );
        }

        return new self(
            $obj->messageId,
            $obj->phoneNumber,
            $obj->sender,
            $attachments,
            $obj->receivedAt,
            $obj->recipient ?? null,
            $obj->simNumber ?? null,
            $obj->subject ?? null,
            $obj->body ?? null
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

    /**
     * @return MmsDownloadedAttachment[]
     */
    public function Attachments(): array {
        return $this->attachments;
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

    public function Body(): ?string {
        return $this->body;
    }
}
