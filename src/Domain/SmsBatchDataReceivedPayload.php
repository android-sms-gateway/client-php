<?php

namespace AndroidSmsGateway\Domain;

/**
 * Payload of an sms:batch:data-received event (list of received data SMS messages).
 */
final class SmsBatchDataReceivedPayload {
    /** @var SmsDataReceivedPayload[] */
    private array $messages;

    /**
     * @param SmsDataReceivedPayload[] $messages
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
                static fn($m) => SmsDataReceivedPayload::FromObject($m),
                $obj->messages
            );
        }

        return new self($messages);
    }

    /**
     * @return SmsDataReceivedPayload[]
     */
    public function Messages(): array {
        return $this->messages;
    }
}
