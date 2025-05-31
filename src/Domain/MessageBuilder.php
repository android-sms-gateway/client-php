<?php

namespace AndroidSmsGateway\Domain;

use InvalidArgumentException;

/**
 * MessageBuilder
 */
class MessageBuilder {
    /**
     * @var string|null
     */
    private ?string $id = null;

    /**
     * @var string
     */
    private string $message;

    /**
     * @var int|null
     */
    private ?int $ttl = null;

    /**
     * @var int|null
     */
    private ?int $simNumber = null;

    /**
     * @var bool
     */
    private bool $withDeliveryReport = true;


    /**
     * @var array<string>
     */
    private array $phoneNumbers;

    /**
     * @var int|null
     */
    private ?int $priority = null;

    /**
     * @var string|null
     */
    private ?string $validUntil = null;

    /**
     * @param string $message
     * @param array<string> $phoneNumbers
     */
    public function __construct(string $message, array $phoneNumbers) {
        $this->message = $message;
        $this->phoneNumbers = $phoneNumbers;
    }

    /**
     * Set message ID
     *
     * @param string|null $id
     * @return $this
     */
    public function setId(?string $id): self {
        $this->id = $id;
        return $this;
    }

    /**
     * Set message text
     *
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): self {
        $this->message = $message;
        return $this;
    }

    /**
     * Set time to live in seconds
     *
     * @param int|null $ttl
     * @return $this
     */
    public function setTtl(?int $ttl): self {
        $this->ttl = $ttl;
        return $this;
    }

    /**
     * Set SIM card number
     *
     * @param int|null $simNumber
     * @return $this
     */
    public function setSimNumber(?int $simNumber): self {
        $this->simNumber = $simNumber;
        return $this;
    }

    /**
     * Set delivery report flag
     *
     * @param bool $withDeliveryReport
     * @return $this
     */
    public function setWithDeliveryReport(bool $withDeliveryReport): self {
        $this->withDeliveryReport = $withDeliveryReport;
        return $this;
    }
    /**
     * Set phone numbers
     *
     * @param array<string> $phoneNumbers
     * @return $this
     */
    public function setPhoneNumbers(array $phoneNumbers): self {
        $this->phoneNumbers = $phoneNumbers;
        return $this;
    }

    /**
     * Set message priority
     *
     * @param int|null $priority
     * @return $this
     */
    public function setPriority(?int $priority): self {
        $this->priority = $priority;
        return $this;
    }

    /**
     * Set valid until timestamp
     *
     * @param string|null $validUntil
     * @return $this
     */
    public function setValidUntil(?string $validUntil): self {
        $this->validUntil = $validUntil;
        return $this;
    }

    /**
     * Build the Message object
     *
     * @return Message
     * @throws InvalidArgumentException
     */
    public function build(): Message {
        if ($this->ttl !== null && $this->validUntil !== null) {
            throw new InvalidArgumentException('validUntil and ttl cannot be set at the same time');
        }

        return new Message(
            $this->message,
            $this->phoneNumbers,
            $this->id,
            $this->ttl,
            $this->simNumber,
            $this->withDeliveryReport,
            $this->priority,
            $this->validUntil
        );
    }
}