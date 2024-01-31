<?php

namespace AndroidSmsGateway\Domain;

use AndroidSmsGateway\Encryptor;
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
     * Is message and phones hashed
     * @var bool
     */
    private bool $isHashed;

    /**
     * Is message and phones encrypted
     * @var bool
     */
    private bool $isEncrypted;

    /**
     * @param array<RecipientState> $recipients
     */
    public function __construct(
        string $id,
        ProcessState $state,
        array $recipients,
        bool $isHashed = false,
        bool $isEncrypted = false
    ) {
        $this->id = $id;
        $this->state = $state;
        $this->recipients = $recipients;
        $this->isHashed = $isHashed;
        $this->isEncrypted = $isEncrypted;
    }

    /**
     * Get message ID
     * @return string
     */
    public function ID(): string {
        return $this->id;
    }

    /**
     * Get message state
     * @return ProcessState
     */
    public function State(): ProcessState {
        return $this->state;
    }

    /**
     * Is message and phones hashed
     * @return bool
     */
    public function IsHashed(): bool {
        return $this->isHashed;
    }

    /**
     * Get recipient states
     * @return array<RecipientState>
     */
    public function Recipients(): array {
        return $this->recipients;
    }

    public function Decrypt(Encryptor $encryptor): self {
        if ($this->isHashed) {
            return $this;
        }

        if (!$this->isEncrypted) {
            return $this;
        }

        $this->recipients = array_map(
            static fn(RecipientState $recipient) => $recipient->Decrypt($encryptor),
            $this->recipients
        );

        $this->isEncrypted = false;

        return $this;
    }

    public static function FromObject(object $obj): self {
        return new self(
            $obj->id,
            ProcessState::FromValue($obj->state),
            array_map(
                static fn($obj) => RecipientState::FromObject($obj),
                $obj->recipients
            ),
            $obj->isHashed ?? false,
            $obj->isEncrypted ?? false
        );
    }
}