<?php

namespace AndroidSmsGateway\Enums;

final class DeviceState {
    public const ACTIVE = 'Active';
    public const DISABLED = 'Disabled';

    private const _ALL_ = [
        self::ACTIVE,
        self::DISABLED,
    ];

    private string $value;

    private function __construct(string $value) {
        if (!in_array($value, self::_ALL_)) {
            throw new \InvalidArgumentException('Invalid value');
        }

        $this->value = $value;
    }

    public static function ACTIVE(): self {
        return new self(self::ACTIVE);
    }

    public static function DISABLED(): self {
        return new self(self::DISABLED);
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
