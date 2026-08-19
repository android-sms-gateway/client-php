<?php

namespace AndroidSmsGateway\Domain;

/**
 * Payload of an mms:batch:downloaded event (list of downloaded MMS messages).
 */
final class MmsBatchDownloadedPayload {
    /** @var MmsDownloadedPayload[] */
    private array $messages;

    /**
     * @param MmsDownloadedPayload[] $messages
     */
    public function __construct(array $messages) {
        $this->messages = $messages;
    }

    /**
     * @param object $obj
     * @return self
     */
    public static function FromObject(object $obj): self {
        $messages = [];
        if (isset($obj->messages) && is_array($obj->messages)) {
            $messages = array_map(
                static fn($m) => MmsDownloadedPayload::FromObject($m),
                $obj->messages
            );
        }

        return new self($messages);
    }

    /**
     * @return MmsDownloadedPayload[]
     */
    public function Messages(): array {
        return $this->messages;
    }
}
