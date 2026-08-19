<?php

namespace AndroidSmsGateway\Tests\Domain;

use AndroidSmsGateway\Domain\InboxRefreshRequest;
use AndroidSmsGateway\Enums\IncomingMessageType;
use AndroidSmsGateway\Enums\WebhookDelivery;
use PHPUnit\Framework\TestCase;

class InboxRefreshRequestTest extends TestCase {
    public function testToObjectOmitsUnsetFields(): void {
        $request = new InboxRefreshRequest(
            null,
            '2026-01-01T00:00:00Z',
            '2026-01-02T00:00:00Z',
            [],
            null
        );

        $this->assertSame([
            'since' => '2026-01-01T00:00:00Z',
            'until' => '2026-01-02T00:00:00Z',
        ], $this->decode($request));
    }

    public function testToObjectEmitsSetFields(): void {
        $request = new InboxRefreshRequest(
            'dev-123',
            '2026-01-01T00:00:00Z',
            '2026-01-02T00:00:00Z',
            [IncomingMessageType::SMS(), IncomingMessageType::DATA_SMS()],
            WebhookDelivery::BATCH()
        );

        $this->assertSame([
            'since' => '2026-01-01T00:00:00Z',
            'until' => '2026-01-02T00:00:00Z',
            'deviceId' => 'dev-123',
            'messageTypes' => ['SMS', 'DATA_SMS'],
            'webhookDelivery' => 'Batch',
        ], $this->decode($request));
    }

    public function testToObjectEmitsWebhookDeliveryOnly(): void {
        $request = new InboxRefreshRequest(
            null,
            '2026-01-01T00:00:00Z',
            '2026-01-02T00:00:00Z',
            [],
            WebhookDelivery::INDIVIDUAL()
        );

        $this->assertSame([
            'since' => '2026-01-01T00:00:00Z',
            'until' => '2026-01-02T00:00:00Z',
            'webhookDelivery' => 'Individual',
        ], $this->decode($request));
    }

    public function testToObjectEmitsSparseMessageTypesAsJsonArray(): void {
        $request = new InboxRefreshRequest(
            null,
            '2026-01-01T00:00:00Z',
            '2026-01-02T00:00:00Z',
            [5 => IncomingMessageType::SMS(), 2 => IncomingMessageType::DATA_SMS()],
            null
        );

        $this->assertSame(['SMS', 'DATA_SMS'], $this->decode($request)['messageTypes']);
        $this->assertSame(
            '{"since":"2026-01-01T00:00:00Z","until":"2026-01-02T00:00:00Z","messageTypes":["SMS","DATA_SMS"]}',
            json_encode($request->ToObject(), JSON_THROW_ON_ERROR)
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function decode(InboxRefreshRequest $request): array {
        $json = json_encode($request->ToObject(), JSON_THROW_ON_ERROR);

        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($json, true);

        return $decoded;
    }
}
