<?php

namespace AndroidSmsGateway\Domain;

/**
 * A single attachment of an MMS message.
 */
final class MmsAttachment {
    /**
     * MIME type of the attachment (e.g. image/png)
     * @var string
     */
    private string $contentType;

    /**
     * Base64-encoded attachment content
     * @var string
     */
    private string $data;

    /**
     * Optional file name of the attachment
     * @var string|null
     */
    private ?string $name;

    /**
     * @param string $contentType MIME type of the attachment (e.g. image/png)
     * @param string $data Base64-encoded attachment content
     * @param string|null $name Optional file name of the attachment
     */
    public function __construct(string $contentType, string $data, ?string $name = null) {
        $this->contentType = $contentType;
        $this->data = $data;
        $this->name = $name;
    }

    public function ContentType(): string {
        return $this->contentType;
    }

    public function Data(): string {
        return $this->data;
    }

    public function Name(): ?string {
        return $this->name;
    }

    public function ToObject(): \stdClass {
        $obj = (object) [
            'contentType' => $this->contentType,
            'name' => $this->name,
            'data' => $this->data,
        ];

        if ($this->name === null) {
            unset($obj->name);
        }

        return $obj;
    }
}
