<?php

namespace AndroidSmsGateway\Domain;

use AndroidSmsGateway\Encryptor;
use AndroidSmsGateway\Enums\ProcessState;

/**
 * Recipient state
 */
class RecipientState {
    /**
     * Recipient's phone number
     */
    protected string $phoneNumber;
    /**
     * Recipient state
     */
    protected ProcessState $state;
    /**
     * Error message
     */
    protected ?string $error;

    public function __construct(string $phoneNumber, ProcessState $state, ?string $error = null) {
        $this->phoneNumber = $phoneNumber;
        $this->state = $state;
        $this->error = $error;
    }

    public function PhoneNumber(): string {
        return $this->phoneNumber;
    }

    public function State(): ProcessState {
        return $this->state;
    }

    public function Error(): ?string {
        return $this->error;
    }

    public function Decrypt(Encryptor $encryptor): self {
        $this->phoneNumber = $encryptor->Decrypt($this->phoneNumber);

        return $this;
    }

    public static function FromObject(object $obj): self {
        return new self(
            $obj->phoneNumber,
            ProcessState::FromValue($obj->state),
            $obj->error ?? null
        );
    }
}