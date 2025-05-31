<?php

namespace AndroidSmsGateway\Domain;

use AndroidSmsGateway\Interfaces\SerializableInterface;

/**
 * Device settings
 */
class Settings implements SerializableInterface {
    /**
     * Encryption settings
     */
    private ?object $encryption;

    /**
     * Gateway settings
     */
    private ?object $gateway;

    /**
     * Logs settings
     */
    private ?object $logs;

    /**
     * Messages settings
     */
    private ?object $messages;

    /**
     * Ping settings
     */
    private ?object $ping;

    /**
     * Webhooks settings
     */
    private ?object $webhooks;

    /**
     * @param object|null $encryption
     * @param object|null $gateway
     * @param object|null $logs
     * @param object|null $messages
     * @param object|null $ping
     * @param object|null $webhooks
     */
    public function __construct(
        ?object $encryption = null,
        ?object $gateway = null,
        ?object $logs = null,
        ?object $messages = null,
        ?object $ping = null,
        ?object $webhooks = null
    ) {
        $this->encryption = $encryption;
        $this->gateway = $gateway;
        $this->logs = $logs;
        $this->messages = $messages;
        $this->ping = $ping;
        $this->webhooks = $webhooks;
    }

    /**
     * @return object|null
     */
    public function Encryption(): ?object {
        return $this->encryption;
    }

    /**
     * @return object|null
     */
    public function Gateway(): ?object {
        return $this->gateway;
    }

    /**
     * @return object|null
     */
    public function Logs(): ?object {
        return $this->logs;
    }

    /**
     * @return object|null
     */
    public function Messages(): ?object {
        return $this->messages;
    }

    /**
     * @return object|null
     */
    public function Ping(): ?object {
        return $this->ping;
    }

    /**
     * @return object|null
     */
    public function Webhooks(): ?object {
        return $this->webhooks;
    }

    /**
     * @param object $obj
     * @return self
     */
    public static function FromObject(object $obj): self {
        return new self(
            $obj->encryption ?? null,
            $obj->gateway ?? null,
            $obj->logs ?? null,
            $obj->messages ?? null,
            $obj->ping ?? null,
            $obj->webhooks ?? null
        );
    }

    /**
     * @return object
     */
    public function ToObject(): object {
        $obj = new \stdClass();

        if ($this->encryption !== null) {
            $obj->encryption = $this->encryption;
        }

        if ($this->gateway !== null) {
            $obj->gateway = $this->gateway;
        }

        if ($this->logs !== null) {
            $obj->logs = $this->logs;
        }

        if ($this->messages !== null) {
            $obj->messages = $this->messages;
        }

        if ($this->ping !== null) {
            $obj->ping = $this->ping;
        }

        if ($this->webhooks !== null) {
            $obj->webhooks = $this->webhooks;
        }

        return $obj;
    }
}