<?php

namespace AndroidSmsGateway\Domain;

/**
 * Metadata for a non-text MMS part (attachment).
 */
class MmsDownloadedAttachment {
    private int $partId;
    private string $contentType;
    private ?string $name;
    private ?string $data;
    private ?int $size;

    public function __construct(
        int $partId,
        string $contentType,
        ?string $name = null,
        ?string $data = null,
        ?int $size = null
    ) {
        $this->partId = $partId;
        $this->contentType = $contentType;
        $this->name = $name;
        $this->data = $data;
        $this->size = $size;
    }

    /**
     * @param object $obj
     * @return self
     */
    public static function FromObject(object $obj): self {
        return new self(
            $obj->partId,
            $obj->contentType,
            $obj->name ?? null,
            $obj->data ?? null,
            $obj->size ?? null
        );
    }

    public function PartId(): int {
        return $this->partId;
    }

    public function ContentType(): string {
        return $this->contentType;
    }

    public function Name(): ?string {
        return $this->name;
    }

    public function Data(): ?string {
        return $this->data;
    }

    public function Size(): ?int {
        return $this->size;
    }
}
