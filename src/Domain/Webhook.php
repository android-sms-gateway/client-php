<?php

namespace AndroidSmsGateway\Domain;

use AndroidSmsGateway\Interfaces\SerializableInterface;
use AndroidSmsGateway\Enums\WebhookEvent;

/**
 * Webhook model
 */
class Webhook implements SerializableInterface {
    /**
     * Webhook ID
     */
    private ?string $id;

    /**
     * Webhook event
     */
    private WebhookEvent $event;

    /**
     * Webhook URL
     */
    private string $url;

    /**
     * Device ID
     */
    private ?string $deviceId;

    /**
     * @param WebhookEvent $event
     * @param string $url
     * @param string|null $id
     * @param string|null $deviceId
     */
    public function __construct(
        WebhookEvent $event,
        string $url,
        ?string $id = null,
        ?string $deviceId = null
    ) {
        $this->id = $id;
        $this->event = $event;
        $this->url = $url;
        $this->deviceId = $deviceId;
    }

    /**
     * @param object $obj
     * @return self
     */
    public static function FromObject(object $obj): self {
        return new self(
            WebhookEvent::FromValue($obj->event),
            $obj->url,
            $obj->id ?? null,
            $obj->deviceId ?? null
        );
    }

    /**
     * @return object
     */
    public function ToObject(): object {
        $obj = (object) [
            'event' => $this->event->Value(),
            'url' => $this->url,
        ];

        if ($this->id !== null) {
            $obj->id = $this->id;
        }

        if ($this->deviceId !== null) {
            $obj->deviceId = $this->deviceId;
        }

        return $obj;
    }

    /**
     * @return string|null
     */
    public function ID(): ?string {
        return $this->id;
    }

    /**
     * @return WebhookEvent
     */
    public function Event(): WebhookEvent {
        return $this->event;
    }

    /**
     * @return string
     */
    public function Url(): string {
        return $this->url;
    }

    /**
     * @return string|null
     */
    public function DeviceId(): ?string {
        return $this->deviceId;
    }

    /**
     * @return string
     */
    public function __toString(): string {
        return sprintf(
            '[id] %s [event] %s [url] %s [device id] %s',
            $this->id,
            $this->event->Value(),
            $this->url,
            $this->deviceId
        );
    }
}