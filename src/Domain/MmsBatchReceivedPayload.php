<?php

namespace AndroidSmsGateway\Domain;

/**
 * Payload of an mms:batch:received event (list of received MMS messages).
 */
final class MmsBatchReceivedPayload {
    /** @var MmsReceivedPayload[] */
    private array $messages;

    /**
     * @param MmsReceivedPayload[] $messages
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
                static fn($m) => MmsReceivedPayload::FromObject($m),
                $obj->messages
            );
        }

        return new self($messages);
    }

    /**
     * @return MmsReceivedPayload[]
     */
    public function Messages(): array {
        return $this->messages;
    }
}
