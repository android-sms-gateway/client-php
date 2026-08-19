<?php

namespace AndroidSmsGateway\Tests\Domain;

use AndroidSmsGateway\Domain\SmsBatchReceivedPayload;
use AndroidSmsGateway\Domain\SmsReceivedPayload;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

final class SmsBatchReceivedPayloadTest extends TestCase {
    public function testCanBeInstantiated(): void {
        $payload = new SmsBatchReceivedPayload([]);
        $this->assertInstanceOf(SmsBatchReceivedPayload::class, $payload);
    }

    public function testFromObjectMapsMessagesToInnerPayloadInstances(): void {
        $json = '{"messages":['
            . '{"messageId":"m1","phoneNumber":"+79990001111","sender":"+79990002222","message":"first","receivedAt":"2026-08-18T10:00:00Z"},'
            . '{"messageId":"m2","phoneNumber":"+79990003333","sender":"+79990004444","recipient":"+79990003333","simNumber":2,"message":"second","receivedAt":"2026-08-18T10:01:00Z"},'
            . '{"messageId":"m3","phoneNumber":"+79990005555","sender":"+79990006666","message":"third","receivedAt":"2026-08-18T10:02:00Z"}'
            . ']}';

        $batch = SmsBatchReceivedPayload::FromObject(self::decodeObject($json));
        $messages = $batch->Messages();

        $this->assertCount(3, $messages);
        $this->assertContainsOnlyInstancesOf(SmsReceivedPayload::class, $messages);
        $this->assertSame('m1', $messages[0]->MessageId());
        $this->assertSame('first', $messages[0]->Message());
        $this->assertNull($messages[0]->Recipient());
        $this->assertSame('m2', $messages[1]->MessageId());
        $this->assertSame('second', $messages[1]->Message());
        $this->assertSame('+79990003333', $messages[1]->Recipient());
        $this->assertSame(2, $messages[1]->SimNumber());
        $this->assertSame('m3', $messages[2]->MessageId());
        $this->assertSame('third', $messages[2]->Message());
    }

    public function testFromObjectWithMissingMessagesReturnsEmptyArray(): void {
        $batch = SmsBatchReceivedPayload::FromObject(self::decodeObject('{}'));

        $this->assertSame([], $batch->Messages());
    }

    public function testFromObjectWithNonArrayMessagesReturnsEmptyArray(): void {
        $batch = SmsBatchReceivedPayload::FromObject(self::decodeObject('{"messages":"not-an-array"}'));

        $this->assertSame([], $batch->Messages());
    }

    public function testFromObjectWithObjectMessagesReturnsEmptyArray(): void {
        $batch = SmsBatchReceivedPayload::FromObject(self::decodeObject('{"messages":{}}'));

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
