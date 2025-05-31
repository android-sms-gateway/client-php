<?php

namespace AndroidSmsGateway\Domain;

use AndroidSmsGateway\Interfaces\SerializableInterface;

/**
 * Log entry model
 */
class LogEntry implements SerializableInterface {
    /**
     * Log entry ID
     */
    private int $id;

    /**
     * Log message
     */
    private string $message;

    /**
     * Log module
     */
    private string $module;

    /**
     * Log priority
     */
    private string $priority;

    /**
     * Log context
     */
    private ?object $context;

    /**
     * Created at timestamp
     */
    private string $createdAt;

    /**
     * @param int $id
     * @param string $message
     * @param string $module
     * @param string $priority
     * @param string $createdAt
     * @param object|null $context
     */
    public function __construct(
        int $id,
        string $message,
        string $module,
        string $priority,
        string $createdAt,
        ?object $context = null
    ) {
        $this->id = $id;
        $this->message = $message;
        $this->module = $module;
        $this->priority = $priority;
        $this->createdAt = $createdAt;
        $this->context = $context;
    }

    /**
     * @param object $obj
     * @return self
     */
    public static function FromObject(object $obj): self {
        return new self(
            $obj->id,
            $obj->message,
            $obj->module,
            $obj->priority,
            $obj->createdAt,
            $obj->context ?? null
        );
    }

    /**
     * @return object
     */
    public function ToObject(): object {
        $obj = (object) [
            'id' => $this->id,
            'message' => $this->message,
            'module' => $this->module,
            'priority' => $this->priority,
            'createdAt' => $this->createdAt,
        ];

        if ($this->context !== null) {
            $obj->context = $this->context;
        }

        return $obj;
    }

    /**
     * @return int
     */
    public function ID(): int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function Message(): string {
        return $this->message;
    }

    /**
     * @return string
     */
    public function Module(): string {
        return $this->module;
    }

    /**
     * @return string
     */
    public function Priority(): string {
        return $this->priority;
    }

    /**
     * @return object|null
     */
    public function Context(): ?object {
        return $this->context;
    }

    /**
     * @return string
     */
    public function CreatedAt(): string {
        return $this->createdAt;
    }
}