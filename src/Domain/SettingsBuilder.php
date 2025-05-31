<?php

namespace AndroidSmsGateway\Domain;

/**
 * SettingsBuilder
 */
class SettingsBuilder {
    /**
     * @var object|null
     */
    private ?object $encryption = null;

    /**
     * @var object|null
     */
    private ?object $gateway = null;

    /**
     * @var object|null
     */
    private ?object $logs = null;

    /**
     * @var object|null
     */
    private ?object $messages = null;

    /**
     * @var object|null
     */
    private ?object $ping = null;

    /**
     * @var object|null
     */
    private ?object $webhooks = null;

    /**
     * Set encryption settings
     *
     * @param object|null $encryption
     * @return $this
     */
    public function setEncryption(?object $encryption): self {
        $this->encryption = $encryption;
        return $this;
    }

    /**
     * Set gateway settings
     *
     * @param object|null $gateway
     * @return $this
     */
    public function setGateway(?object $gateway): self {
        $this->gateway = $gateway;
        return $this;
    }

    /**
     * Set logs settings
     *
     * @param object|null $logs
     * @return $this
     */
    public function setLogs(?object $logs): self {
        $this->logs = $logs;
        return $this;
    }

    /**
     * Set messages settings
     *
     * @param object|null $messages
     * @return $this
     */
    public function setMessages(?object $messages): self {
        $this->messages = $messages;
        return $this;
    }

    /**
     * Set ping settings
     *
     * @param object|null $ping
     * @return $this
     */
    public function setPing(?object $ping): self {
        $this->ping = $ping;
        return $this;
    }

    /**
     * Set webhooks settings
     *
     * @param object|null $webhooks
     * @return $this
     */
    public function setWebhooks(?object $webhooks): self {
        $this->webhooks = $webhooks;
        return $this;
    }

    /**
     * Build the Settings object
     *
     * @return Settings
     */
    public function build(): Settings {
        return new Settings(
            $this->encryption,
            $this->gateway,
            $this->logs,
            $this->messages,
            $this->ping,
            $this->webhooks
        );
    }
}