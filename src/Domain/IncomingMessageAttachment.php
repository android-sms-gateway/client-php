<?php

namespace AndroidSmsGateway\Domain;

/**
 * Metadata for an MMS attachment returned by the inbox API.
 */
class IncomingMessageAttachment {
    private int $partId;
    private string $name;
    private int $size;
    private string $contentType;

    public function __construct(
        int $partId,
        string $name,
        int $size,
        string $contentType
    ) {
        $this->partId = $partId;
        $this->name = $name;
        $this->size = $size;
        $this->contentType = $contentType;
    }

    /**
     * @param object $obj
     * @return self
     */
    public static function FromObject(object $obj): self {
        return new self(
            $obj->partId,
            $obj->name,
            $obj->size,
            $obj->contentType
        );
    }

    public function PartId(): int {
        return $this->partId;
    }

    public function Name(): string {
        return $this->name;
    }

    public function Size(): int {
        return $this->size;
    }

    public function ContentType(): string {
        return $this->contentType;
    }
}
