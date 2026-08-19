<?php

namespace AndroidSmsGateway\Domain;

/**
 * Payload of an sms:batch:received event (list of received SMS messages).
 */
final class SmsBatchReceivedPayload {
    /** @var SmsReceivedPayload[] */
    private array $messages;

    /**
     * @param SmsReceivedPayload[] $messages
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
                static fn($m) => SmsReceivedPayload::FromObject($m),
                $obj->messages
            );
        }

        return new self($messages);
    }

    /**
     * @return SmsReceivedPayload[]
     */
    public function Messages(): array {
        return $this->messages;
    }
}
