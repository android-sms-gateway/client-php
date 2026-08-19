<?php

namespace AndroidSmsGateway\Tests\Domain;

use AndroidSmsGateway\Domain\SmsReceivedPayload;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

final class SmsReceivedPayloadTest extends TestCase {
    /**
     * @return array<string, array{string, string, string, string, ?string, ?int, string, string}>
     */
    public static function payloadProvider(): array {
        return [
            'with recipient and simNumber' => [
                '{"messageId":"msg-1","phoneNumber":"+79990001111","sender":"+79990001234","recipient":"+79990001111","simNumber":1,"message":"Hello World!","receivedAt":"2026-08-18T10:00:00Z"}',
                'msg-1',
                '+79990001111',
                '+79990001234',
                '+79990001111',
                1,
                'Hello World!',
                '2026-08-18T10:00:00Z'
            ],
            'without optional fields' => [
                '{"messageId":"msg-2","phoneNumber":"+79990002222","sender":"+79990003333","message":"","receivedAt":"2026-08-18T11:00:00Z"}',
                'msg-2',
                '+79990002222',
                '+79990003333',
                null,
                null,
                '',
                '2026-08-18T11:00:00Z'
            ],
            'extreme values' => [
                '{"messageId":"msg-3-very-long-identifier","phoneNumber":"+100000000000000","sender":"","recipient":"+100000000000000","simNumber":0,"message":"First line\nSecond line","receivedAt":"1970-01-01T00:00:00Z"}',
                'msg-3-very-long-identifier',
                '+100000000000000',
                '',
                '+100000000000000',
                0,
                "First line\nSecond line",
                '1970-01-01T00:00:00Z'
            ]
        ];
    }

    public function testCanBeInstantiated(): void {
        $payload = new SmsReceivedPayload('msg-1', '+79990001111', '+79990001234', '+79990001111', 1, 'Hello', '2026-08-18T10:00:00Z');
        $this->assertInstanceOf(SmsReceivedPayload::class, $payload);
    }

    /**
     * @dataProvider payloadProvider
     */
    public function testFromObjectParsesAllFields(
        string $json,
        string $messageId,
        string $phoneNumber,
        string $sender,
        ?string $recipient,
        ?int $simNumber,
        string $message,
        string $receivedAt
    ): void {
        $payload = SmsReceivedPayload::FromObject(self::decodeObject($json));

        $this->assertSame($messageId, $payload->MessageId());
        $this->assertSame($phoneNumber, $payload->PhoneNumber());
        $this->assertSame($sender, $payload->Sender());
        $this->assertSame($recipient, $payload->Recipient());
        $this->assertSame($simNumber, $payload->SimNumber());
        $this->assertSame($message, $payload->Message());
        $this->assertSame($receivedAt, $payload->ReceivedAt());
    }

    public function testFromObjectDefaultsAbsentOptionalFieldsToNull(): void {
        $payload = SmsReceivedPayload::FromObject(self::decodeObject(
            '{"messageId":"msg-4","phoneNumber":"+79990004444","sender":"+79990005555","message":"No extras","receivedAt":"2026-08-18T12:00:00Z"}'
        ));

        $this->assertNull($payload->Recipient());
        $this->assertNull($payload->SimNumber());
    }

    private static function decodeObject(string $json): stdClass {
        $obj = json_decode($json, false);
        if (!$obj instanceof stdClass) {
            throw new RuntimeException('Test fixture must decode to an object: ' . $json);
        }

        return $obj;
    }
}
