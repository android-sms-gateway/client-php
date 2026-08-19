<?php

namespace AndroidSmsGateway\Enums;

final class WebhookEvent {
    public const SMS_RECEIVED = 'sms:received';
    public const SMS_SENT = 'sms:sent';
    public const SMS_DELIVERED = 'sms:delivered';
    public const SMS_FAILED = 'sms:failed';
    public const SMS_CANCELLED = 'sms:cancelled';
    public const SYSTEM_PING = 'system:ping';
    public const APP_STARTED = 'app:started';
    public const MMS_RECEIVED = 'mms:received';
    public const MMS_DOWNLOADED = 'mms:downloaded';
    public const SMS_BATCH_RECEIVED = 'sms:batch:received';
    public const SMS_DATA_BATCH_RECEIVED = 'sms:batch:data-received';
    public const MMS_BATCH_RECEIVED = 'mms:batch:received';
    public const MMS_BATCH_DOWNLOADED = 'mms:batch:downloaded';

    private const _ALL_ = [
        self::SMS_RECEIVED,
        self::SMS_SENT,
        self::SMS_DELIVERED,
        self::SMS_FAILED,
        self::SMS_CANCELLED,
        self::SYSTEM_PING,
        self::APP_STARTED,
        self::MMS_RECEIVED,
        self::MMS_DOWNLOADED,
        self::SMS_BATCH_RECEIVED,
        self::SMS_DATA_BATCH_RECEIVED,
        self::MMS_BATCH_RECEIVED,
        self::MMS_BATCH_DOWNLOADED,
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

    public static function SMS_CANCELLED(): self {
        return new self(self::SMS_CANCELLED);
    }

    public static function SYSTEM_PING(): self {
        return new self(self::SYSTEM_PING);
    }

    public static function APP_STARTED(): self {
        return new self(self::APP_STARTED);
    }

    public static function MMS_RECEIVED(): self {
        return new self(self::MMS_RECEIVED);
    }

    public static function MMS_DOWNLOADED(): self {
        return new self(self::MMS_DOWNLOADED);
    }

    public static function SMS_BATCH_RECEIVED(): self {
        return new self(self::SMS_BATCH_RECEIVED);
    }

    public static function SMS_DATA_BATCH_RECEIVED(): self {
        return new self(self::SMS_DATA_BATCH_RECEIVED);
    }

    public static function MMS_BATCH_RECEIVED(): self {
        return new self(self::MMS_BATCH_RECEIVED);
    }

    public static function MMS_BATCH_DOWNLOADED(): self {
        return new self(self::MMS_BATCH_DOWNLOADED);
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
