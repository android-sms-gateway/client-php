<?php

namespace AndroidSmsGateway\Domain;

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
     * @param string $id
     * @param string $name
     * @param string $createdAt
     * @param string $updatedAt
     * @param string|null $deletedAt
     * @param string|null $lastSeen
     */
    public function __construct(
        string $id,
        string $name,
        string $createdAt,
        string $updatedAt,
        ?string $deletedAt = null,
        ?string $lastSeen = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->deletedAt = $deletedAt;
        $this->lastSeen = $lastSeen;
    }

    /**
     * @param object $obj
     * @return self
     */
    public static function FromObject(object $obj): self {
        return new self(
            $obj->id,
            $obj->name,
            $obj->createdAt,
            $obj->updatedAt,
            $obj->deletedAt ?? null,
            $obj->lastSeen ?? null
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
     * @return string
     */
    public function __toString(): string {
        return sprintf(
            '[id] %s [name] %s [created at] %s [updated at] %s [deleted at] %s [last seen] %s',
            $this->id,
            $this->name,
            $this->createdAt,
            $this->updatedAt,
            $this->deletedAt ?? '',
            $this->lastSeen ?? ''
        );
    }
}