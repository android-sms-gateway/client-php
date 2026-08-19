<?php

namespace AndroidSmsGateway\Tests\Domain;

use AndroidSmsGateway\Domain\SmsDataReceivedPayload;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

final class SmsDataReceivedPayloadTest extends TestCase {
    /**
     * @return array<string, array{string, string, string, string, ?string, ?int, string, string}>
     */
    public static function payloadProvider(): array {
        return [
            'with recipient and simNumber' => [
                '{"messageId":"data-1","phoneNumber":"+79990001111","sender":"+79990001234","recipient":"+79990001111","simNumber":2,"data":"SGVsbG8gV29ybGQh","receivedAt":"2026-08-18T10:00:00Z"}',
                'data-1',
                '+79990001111',
                '+79990001234',
                '+79990001111',
                2,
                'SGVsbG8gV29ybGQh',
                '2026-08-18T10:00:00Z'
            ],
            'without optional fields and empty data' => [
                '{"messageId":"data-2","phoneNumber":"+79990002222","sender":"+79990003333","data":"","receivedAt":"2026-08-18T11:00:00Z"}',
                'data-2',
                '+79990002222',
                '+79990003333',
                null,
                null,
                '',
                '2026-08-18T11:00:00Z'
            ],
            'extreme values' => [
                '{"messageId":"data-3-very-long-identifier","phoneNumber":"+100000000000000","sender":"","recipient":"+100000000000000","simNumber":0,"data":"QUJDREVGR0hJSktMTU5PUFFSU1RVVldYWVphYmNkZWZnaGlqa2xtbm9wcXJzdHV2d3h5ejAxMjM0NTY3ODk=","receivedAt":"1970-01-01T00:00:00Z"}',
                'data-3-very-long-identifier',
                '+100000000000000',
                '',
                '+100000000000000',
                0,
                'QUJDREVGR0hJSktMTU5PUFFSU1RVVldYWVphYmNkZWZnaGlqa2xtbm9wcXJzdHV2d3h5ejAxMjM0NTY3ODk=',
                '1970-01-01T00:00:00Z'
            ]
        ];
    }

    public function testCanBeInstantiated(): void {
        $payload = new SmsDataReceivedPayload('data-1', '+79990001111', '+79990001234', '+79990001111', 2, 'SGVsbG8=', '2026-08-18T10:00:00Z');
        $this->assertInstanceOf(SmsDataReceivedPayload::class, $payload);
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
        string $data,
        string $receivedAt
    ): void {
        $payload = SmsDataReceivedPayload::FromObject(self::decodeObject($json));

        $this->assertSame($messageId, $payload->MessageId());
        $this->assertSame($phoneNumber, $payload->PhoneNumber());
        $this->assertSame($sender, $payload->Sender());
        $this->assertSame($recipient, $payload->Recipient());
        $this->assertSame($simNumber, $payload->SimNumber());
        $this->assertSame($data, $payload->Data());
        $this->assertSame($receivedAt, $payload->ReceivedAt());
    }

    public function testFromObjectDefaultsAbsentOptionalFieldsToNull(): void {
        $payload = SmsDataReceivedPayload::FromObject(self::decodeObject(
            '{"messageId":"data-4","phoneNumber":"+79990004444","sender":"+79990005555","data":"SGVsbG8=","receivedAt":"2026-08-18T12:00:00Z"}'
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
