<?php

namespace AndroidSmsGateway\Domain;

/**
 * Data message content
 */
class DataMessage {
    /**
     * Base64-encoded data
     * @var string
     */
    private string $data;

    /**
     * Destination port (0-65535)
     * @var int
     */
    private int $port;

    public function __construct(string $data, int $port) {
        if ($port < 0 || $port > 65535) {
            throw new \InvalidArgumentException('Port must be between 0 and 65535');
        }
        $this->data = $data;
        $this->port = $port;
    }

    /**
     * Get base64-encoded data
     * @return string
     */
    public function Data(): string {
        return $this->data;
    }

    /**
     * Get destination port
     * @return int
     */
    public function Port(): int {
        return $this->port;
    }

    public static function FromObject(object $obj): self {
        return new self(
            $obj->data,
            $obj->port
        );
    }
}
