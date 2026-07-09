<?php

namespace AndroidSmsGateway\Domain;

/**
 * Text message content
 */
class TextMessage {
    /**
     * Message text
     * @var string
     */
    private string $text;

    public function __construct(string $text) {
        $this->text = $text;
    }

    /**
     * Get message text
     * @return string
     */
    public function Text(): string {
        return $this->text;
    }

    public static function FromObject(object $obj): self {
        return new self(
            $obj->text
        );
    }
}
