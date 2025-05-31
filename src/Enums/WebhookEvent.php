<?php

namespace AndroidSmsGateway\Enums;

final class WebhookEvent {
    public const SMS_RECEIVED = 'sms:received';
    public const SMS_SENT = 'sms:sent';
    public const SMS_DELIVERED = 'sms:delivered';
    public const SMS_FAILED = 'sms:failed';
    public const SYSTEM_PING = 'system:ping';

    private const _ALL_ = [
        self::SMS_RECEIVED,
        self::SMS_SENT,
        self::SMS_DELIVERED,
        self::SMS_FAILED,
        self::SYSTEM_PING,
    ];

    private string $value;

    private function __construct(string $value) {
        if (!in_array($value, self::_ALL_)) {
            throw new \InvalidArgumentException('Invalid value');
        }

        $this->value = $value;
    }

    public static function SMS_RECEIVED(): self {
        return new self(self::SMS_RECEIVED);
    }

    public static function SMS_SENT(): self {
        return new self(self::SMS_SENT);
    }

    public static function SMS_DELIVERED(): self {
        return new self(self::SMS_DELIVERED);
    }

    public static function SMS_FAILED(): self {
        return new self(self::SMS_FAILED);
    }

    public static function SYSTEM_PING(): self {
        return new self(self::SYSTEM_PING);
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
}
