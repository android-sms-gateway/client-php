<?php

namespace AndroidSmsGateway\Domain;

use AndroidSmsGateway\Enums\IncomingMessageType;
use AndroidSmsGateway\Enums\WebhookDelivery;
use AndroidSmsGateway\Interfaces\SerializableInterface;

/**
 * Inbox refresh request
 */
class InboxRefreshRequest implements SerializableInterface {
    /**
     * ID of the device to refresh messages for
     */
    private ?string $deviceId;

    /**
     * Start of the time range to refresh
     */
    private string $since;

    /**
     * End of the time range to refresh
     */
    private string $until;

    /**
     * List of message types to refresh
     *
     * @var array<int, IncomingMessageType>
     */
    private array $messageTypes;

    /**
     * Delivery mode for webhooks
     */
    private ?WebhookDelivery $webhookDelivery;


    /**
     * @param string|null $deviceId
     * @param string $since
     * @param string $until
     * @param array<int, IncomingMessageType> $messageTypes
     * @param WebhookDelivery|null $webhookDelivery
     */
    public function __construct(
        ?string $deviceId,
        string $since,
        string $until,
        array $messageTypes,
        ?WebhookDelivery $webhookDelivery = null
    ) {
        $this->deviceId = $deviceId;
        $this->since = $since;
        $this->until = $until;
        $this->messageTypes = $messageTypes;
        $this->webhookDelivery = $webhookDelivery;
    }

    /**
     * @return object
     */
    public function ToObject(): object {
        $obj = [
            'since' => $this->since,
            'until' => $this->until,
        ];

        if ($this->deviceId !== null) {
            $obj['deviceId'] = $this->deviceId;
        }

        if (!empty($this->messageTypes)) {
            $obj['messageTypes'] = array_values(array_map(
                static fn(IncomingMessageType $type): string => $type->Value(),
                $this->messageTypes
            ));
        }

        if ($this->webhookDelivery !== null) {
            $obj['webhookDelivery'] = $this->webhookDelivery->Value();
        }

        return (object) $obj;
    }
}
