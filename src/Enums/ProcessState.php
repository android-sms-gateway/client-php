<?php

namespace AndroidSmsGateway\Enums;

/**
 * Message state
 */
final class ProcessState {
    public const PENDING = 'Pending';
    public const CANCELLING = 'Cancelling';
    public const CANCELLED = 'Cancelled';
    public const PROCESSED = 'Processed';
    public const SENT = 'Sent';
    public const DELIVERED = 'Delivered';
    public const FAILED = 'Failed';

    private string $value;

    private function __construct(string $value) {
        if (!in_array($value, self::_ALL_)) {
            throw new \InvalidArgumentException('Invalid value');
        }

        $this->value = $value;
    }

    /**
     * Pending - Message has been created, but not yet received by device
     * @return self
     */
    public static function PENDING(): self {
        return new self(self::PENDING);
    }

    /**
     * Cancelling - Cancellation has been requested
     * @return self
     */
    public static function CANCELLING(): self {
        return new self(self::CANCELLING);
    }

    /**
     * Cancelled - Message has been cancelled
     * @return self
     */
    public static function CANCELLED(): self {
        return new self(self::CANCELLED);
    }

    /**
     * Processed - Message has been received by device
     * @return self
     */
    public static function PROCESSED(): self {
        return new self(self::PROCESSED);
    }

    /**
     * Sent - Message has been sent
     * @return self
     */
    public static function SENT(): self {
        return new self(self::SENT);
    }

    /**
     * Delivered - Message has been delivered
     * @return self
     */
    public static function DELIVERED(): self {
        return new self(self::DELIVERED);
    }

    /**
     * Failed - Message has failed
     * @return self
     */
    public static function FAILED(): self {
        return new self(self::FAILED);
    }

    public static function FromValue(string $value): self {
        return new self($value);
    }

    public function Value(): string {
        return $this->value;
    }

    public function __toString(): string {
        return $this->value;
    }

    private const _ALL_ = [
        self::PENDING,
        self::CANCELLING,
        self::CANCELLED,
        self::PROCESSED,
        self::SENT,
        self::DELIVERED,
        self::FAILED,
    ];
}