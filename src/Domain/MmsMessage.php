<?php

namespace AndroidSmsGateway\Domain;

/**
 * An MMS message with optional subject, text and attachments.
 */
final class MmsMessage {
    /**
     * Optional subject of the MMS
     * @var string|null
     */
    private ?string $subject;

    /**
     * Optional text body of the MMS
     * @var string|null
     */
    private ?string $text;

    /**
     * List of attachments. Omitted entirely from the wire when empty.
     * @var array<MmsAttachment>
     */
    private array $attachments;

    /**
     * @param string|null $subject Optional subject of the MMS
     * @param string|null $text Optional text body of the MMS
     * @param array<MmsAttachment> $attachments List of attachments
     */
    public function __construct(?string $subject = null, ?string $text = null, array $attachments = []) {
        $this->subject = $subject;
        $this->text = $text;
        $this->attachments = $attachments;
    }

    public function Subject(): ?string {
        return $this->subject;
    }

    public function Text(): ?string {
        return $this->text;
    }

    /**
     * @return array<MmsAttachment>
     */
    public function Attachments(): array {
        return $this->attachments;
    }

    public function ToObject(): \stdClass {
        $obj = (object) [
            'subject' => $this->subject,
            'text' => $this->text,
            'attachments' => array_map(
                static fn(MmsAttachment $attachment) => $attachment->ToObject(),
                $this->attachments
            ),
        ];

        if ($this->subject === null) {
            unset($obj->subject);
        }

        if ($this->text === null) {
            unset($obj->text);
        }

        if (count($this->attachments) === 0) {
            unset($obj->attachments);
        }

        return $obj;
    }
}
