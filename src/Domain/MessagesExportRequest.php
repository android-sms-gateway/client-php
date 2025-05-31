<?php

namespace AndroidSmsGateway\Domain;

use AndroidSmsGateway\Interfaces\SerializableInterface;

/**
 * Messages export request
 */
class MessagesExportRequest implements SerializableInterface {
    /**
     * Device ID
     */
    private string $deviceId;

    /**
     * Start of time range
     */
    private string $since;

    /**
     * End of time range
     */
    private string $until;

    /**
     * @param string $deviceId
     * @param string $since
     * @param string $until
     */
    public function __construct(string $deviceId, string $since, string $until) {
        $this->deviceId = $deviceId;
        $this->since = $since;
        $this->until = $until;
    }

    /**
     * @param object $obj
     * @return self
     */
    public static function FromObject(object $obj): self {
        return new self(
            $obj->deviceId,
            $obj->since,
            $obj->until
        );
    }

    /**
     * @return object
     */
    public function ToObject(): object {
        return (object) [
            'deviceId' => $this->deviceId,
            'since' => $this->since,
            'until' => $this->until,
        ];
    }
}