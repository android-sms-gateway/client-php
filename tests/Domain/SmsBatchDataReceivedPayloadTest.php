<?php

namespace AndroidSmsGateway\Tests\Domain;

use AndroidSmsGateway\Domain\SmsBatchDataReceivedPayload;
use AndroidSmsGateway\Domain\SmsDataReceivedPayload;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

final class SmsBatchDataReceivedPayloadTest extends TestCase {
    public function testCanBeInstantiated(): void {
        $payload = new SmsBatchDataReceivedPayload([]);
        $this->assertInstanceOf(SmsBatchDataReceivedPayload::class, $payload);
    }

    public function testFromObjectMapsMessagesToInnerPayloadInstances(): void {
        $json = '{"messages":['
            . '{"messageId":"d1","phoneNumber":"+79990001111","sender":"+79990002222","data":"SGVsbG8=","receivedAt":"2026-08-18T10:00:00Z"},'
            . '{"messageId":"d2","phoneNumber":"+79990003333","sender":"+79990004444","recipient":"+79990003333","simNumber":2,"data":"V29ybGQ=","receivedAt":"2026-08-18T10:01:00Z"},'
            . '{"messageId":"d3","phoneNumber":"+79990005555","sender":"+79990006666","data":"IQ==","receivedAt":"2026-08-18T10:02:00Z"}'
            . ']}';

        $batch = SmsBatchDataReceivedPayload::FromObject(self::decodeObject($json));
        $messages = $batch->Messages();

        $this->assertCount(3, $messages);
        $this->assertContainsOnlyInstancesOf(SmsDataReceivedPayload::class, $messages);
        $this->assertSame('d1', $messages[0]->MessageId());
        $this->assertSame('SGVsbG8=', $messages[0]->Data());
        $this->assertNull($messages[0]->Recipient());
        $this->assertSame('d2', $messages[1]->MessageId());
        $this->assertSame('V29ybGQ=', $messages[1]->Data());
        $this->assertSame('+79990003333', $messages[1]->Recipient());
        $this->assertSame(2, $messages[1]->SimNumber());
        $this->assertSame('d3', $messages[2]->MessageId());
        $this->assertSame('IQ==', $messages[2]->Data());
    }

    public function testFromObjectWithMissingMessagesReturnsEmptyArray(): void {
        $batch = SmsBatchDataReceivedPayload::FromObject(self::decodeObject('{}'));

        $this->assertSame([], $batch->Messages());
    }

    public function testFromObjectWithNonArrayMessagesReturnsEmptyArray(): void {
        $batch = SmsBatchDataReceivedPayload::FromObject(self::decodeObject('{"messages":"not-an-array"}'));

        $this->assertSame([], $batch->Messages());
    }

    public function testFromObjectWithObjectMessagesReturnsEmptyArray(): void {
        $batch = SmsBatchDataReceivedPayload::FromObject(self::decodeObject('{"messages":{}}'));

        $this->assertSame([], $batch->Messages());
    }

    private static function decodeObject(string $json): stdClass {
        $obj = json_decode($json, false);
        if (!$obj instanceof stdClass) {
            throw new RuntimeException('Test fixture must decode to an object: ' . $json);
        }

        return $obj;
    }
}
