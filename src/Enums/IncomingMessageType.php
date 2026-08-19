<?php

namespace AndroidSmsGateway\Enums;

final class IncomingMessageType {
    public const SMS = 'SMS';
    public const DATA_SMS = 'DATA_SMS';
    public const MMS = 'MMS';
    public const MMS_DOWNLOADED = 'MMS_DOWNLOADED';

    private const _ALL_ = [
        self::SMS,
        self::DATA_SMS,
        self::MMS,
        self::MMS_DOWNLOADED,
    ];

    private string $value;

    private function __construct(string $value) {
        if (!in_array($value, self::_ALL_)) {
            throw new \InvalidArgumentException('Invalid value');
        }

        $this->value = $value;
    }

    public static function SMS(): self {
        return new self(self::SMS);
    }

    public static function DATA_SMS(): self {
        return new self(self::DATA_SMS);
    }

    public static function MMS(): self {
        return new self(self::MMS);
    }

    public static function MMS_DOWNLOADED(): self {
        return new self(self::MMS_DOWNLOADED);
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
