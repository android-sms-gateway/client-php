<?php

namespace AndroidSmsGateway\Domain;

/**
 * An incoming (received) message from the inbox.
 */
class IncomingMessage {
    private string $id;
    private string $type;
    private string $sender;
    private string $contentPreview;
    private string $createdAt;
    private ?string $recipient;
    private ?int $simNumber;
    /** @var IncomingMessageAttachment[]|null */
    private ?array $attachments;

    /**
     * @param string $id
     * @param string $type
     * @param string $sender
     * @param string $contentPreview
     * @param string $createdAt
     * @param string|null $recipient
     * @param int|null $simNumber
     * @param IncomingMessageAttachment[]|null $attachments
     */
    public function __construct(
        string $id,
        string $type,
        string $sender,
        string $contentPreview,
        string $createdAt,
        ?string $recipient = null,
        ?int $simNumber = null,
        ?array $attachments = null
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->sender = $sender;
        $this->contentPreview = $contentPreview;
        $this->createdAt = $createdAt;
        $this->recipient = $recipient;
        $this->simNumber = $simNumber;
        $this->attachments = $attachments;
    }

    /**
     * @param object $obj
     * @return self
     */
    public static function FromObject(object $obj): self {
        $attachments = null;
        if (isset($obj->attachments) && is_array($obj->attachments)) {
            $attachments = array_map(
                static fn($a) => IncomingMessageAttachment::FromObject($a),
                $obj->attachments
            );
        }

        return new self(
            $obj->id,
            $obj->type,
            $obj->sender,
            $obj->contentPreview,
            $obj->createdAt,
            $obj->recipient ?? null,
            $obj->simNumber ?? null,
            $attachments
        );
    }

    public function ID(): string {
        return $this->id;
    }

    public function Type(): string {
        return $this->type;
    }

    public function Sender(): string {
        return $this->sender;
    }

    public function ContentPreview(): string {
        return $this->contentPreview;
    }

    public function CreatedAt(): string {
        return $this->createdAt;
    }

    public function Recipient(): ?string {
        return $this->recipient;
    }

    public function SimNumber(): ?int {
        return $this->simNumber;
    }

    /**
     * @return IncomingMessageAttachment[]|null
     */
    public function Attachments(): ?array {
        return $this->attachments;
    }
}
