<?php

namespace AndroidSmsGateway\Enums;

final class WebhookDelivery {
    public const DISABLED = 'Disabled';
    public const INDIVIDUAL = 'Individual';
    public const BATCH = 'Batch';

    private const _ALL_ = [
        self::DISABLED,
        self::INDIVIDUAL,
        self::BATCH,
    ];

    private string $value;

    private function __construct(string $value) {
        if (!in_array($value, self::_ALL_)) {
            throw new \InvalidArgumentException('Invalid value');
        }

        $this->value = $value;
    }

    public static function DISABLED(): self {
        return new self(self::DISABLED);
    }

    public static function INDIVIDUAL(): self {
        return new self(self::INDIVIDUAL);
    }

    public static function BATCH(): self {
        return new self(self::BATCH);
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
