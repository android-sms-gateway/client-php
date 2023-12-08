<?php

namespace AndroidSmsGateway\Domain;

use AndroidSmsGateway\Enums\ProcessState;

/**
 * Message state
 */
class MessageState {
    /**
     * Message ID
     * @var string
     */
    private string $id;
    /**
     * Message state
     * @var ProcessState
     */
    private ProcessState $state;
    /**
     * Recipient states
     * @var array<RecipientState>
     */
    private array $recipients;

    /**
     * @param array<RecipientState> $recipients
     */
    public function __construct(string $id, ProcessState $state, array $recipients) {
        $this->id = $id;
        $this->state = $state;
        $this->recipients = $recipients;
    }

    public function ID(): string {
        return $this->id;
    }

    public function State(): ProcessState {
        return $this->state;
    }

    /**
     * @return array<RecipientState>
     */
    public function Recipients(): array {
        return $this->recipients;
    }

    public static function FromObject(object $obj): self {
        return new self(
            $obj->id,
            ProcessState::FromValue($obj->state),
            array_map(
                static fn($obj) => RecipientState::FromObject($obj),
                $obj->recipients
            )
        );
    }
}