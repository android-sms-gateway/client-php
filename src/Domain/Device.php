<?php

namespace AndroidSmsGateway\Domain;

use AndroidSmsGateway\Enums\DeviceState;
use AndroidSmsGateway\Interfaces\SerializableInterface;

/**
 * Device model
 */
class Device implements SerializableInterface {
    /**
     * Device ID
     */
    private string $id;

    /**
     * Device name
     */
    private string $name;

    /**
     * Created at timestamp
     */
    private string $createdAt;

    /**
     * Updated at timestamp
     */
    private string $updatedAt;

    /**
     * Deleted at timestamp
     */
    private ?string $deletedAt;

    /**
     * Last seen timestamp
     */
    private ?string $lastSeen;

    /**
     * Device state
     */
    private ?DeviceState $state;

    /**
     * @param string $id
     * @param string $name
     * @param string $createdAt
     * @param string $updatedAt
     * @param string|null $deletedAt
     * @param string|null $lastSeen
     * @param DeviceState|null $state
     */
    public function __construct(
        string $id,
        string $name,
        string $createdAt,
        string $updatedAt,
        ?string $deletedAt = null,
        ?string $lastSeen = null,
        ?DeviceState $state = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->deletedAt = $deletedAt;
        $this->lastSeen = $lastSeen;
        $this->state = $state;
    }

    /**
     * @param object $obj
     * @return self
     */
    public static function FromObject(object $obj): self {
        $state = isset($obj->state) && is_string($obj->state)
            ? DeviceState::FromValue($obj->state)
            : null;

        return new self(
            $obj->id,
            $obj->name,
            $obj->createdAt,
            $obj->updatedAt,
            $obj->deletedAt ?? null,
            $obj->lastSeen ?? null,
            $state
        );
    }

    /**
     * @return object
     */
    public function ToObject(): object {
        $obj = (object) [
            'id' => $this->id,
            'name' => $this->name,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];

        if ($this->deletedAt !== null) {
            $obj->deletedAt = $this->deletedAt;
        }

        if ($this->lastSeen !== null) {
            $obj->lastSeen = $this->lastSeen;
        }

        if ($this->state !== null) {
            $obj->state = $this->state->Value();
        }

        return $obj;
    }

    /**
     * Get device ID
     * @return string
     */
    public function ID(): string {
        return $this->id;
    }

    /**
     * Get device name
     * @return string
     */
    public function Name(): string {
        return $this->name;
    }

    /**
     * Get created at timestamp
     * @return string
     */
    public function CreatedAt(): string {
        return $this->createdAt;
    }

    /**
     * Get updated at timestamp
     * @return string
     */
    public function UpdatedAt(): string {
        return $this->updatedAt;
    }

    /**
     * Get deleted at timestamp
     * @return string|null
     */
    public function DeletedAt(): ?string {
        return $this->deletedAt;
    }

    /**
     * Get last seen timestamp
     * @return string|null
     */
    public function LastSeen(): ?string {
        return $this->lastSeen;
    }

    /**
     * Get device state
     * @return DeviceState|null
     */
    public function State(): ?DeviceState {
        return $this->state;
    }

    /**
     * @return string
     */
    public function __toString(): string {
        return sprintf(
            '[id] %s [name] %s [created at] %s [updated at] %s [deleted at] %s [last seen] %s [state] %s',
            $this->id,
            $this->name,
            $this->createdAt,
            $this->updatedAt,
            $this->deletedAt ?? '',
            $this->lastSeen ?? '',
            $this->state !== null ? $this->state->Value() : ''
        );
    }
}