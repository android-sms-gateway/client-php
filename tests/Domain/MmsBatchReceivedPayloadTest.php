<?php

namespace AndroidSmsGateway\Tests\Domain;

use AndroidSmsGateway\Domain\MmsBatchReceivedPayload;
use AndroidSmsGateway\Domain\MmsReceivedPayload;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

final class MmsBatchReceivedPayloadTest extends TestCase {
    public function testCanBeInstantiated(): void {
        $payload = new MmsBatchReceivedPayload([]);
        $this->assertInstanceOf(MmsBatchReceivedPayload::class, $payload);
    }

    public function testFromObjectMapsMessagesToInnerPayloadInstances(): void {
        $json = '{"messages":['
            . '{"messageId":"mms-1","phoneNumber":"+79990001111","sender":"+79990002222","transactionId":"tx-1","contentClass":"MMS","size":1024,"receivedAt":"2026-08-18T10:00:00Z"},'
            . '{"messageId":"mms-2","phoneNumber":"+79990003333","sender":"+79990004444","transactionId":"tx-2","contentClass":"text","size":0,"receivedAt":"2026-08-18T10:01:00Z","recipient":"+79990003333","simNumber":1,"subject":"Hello"},'
            . '{"messageId":"mms-3","phoneNumber":"+79990005555","sender":"+79990006666","transactionId":"tx-3","contentClass":"image","size":1048576,"receivedAt":"2026-08-18T10:02:00Z"}'
            . ']}';

        $batch = MmsBatchReceivedPayload::FromObject(self::decodeObject($json));
        $messages = $batch->Messages();

        $this->assertCount(3, $messages);
        $this->assertContainsOnlyInstancesOf(MmsReceivedPayload::class, $messages);
        $this->assertSame('mms-1', $messages[0]->MessageId());
        $this->assertSame('tx-1', $messages[0]->TransactionId());
        $this->assertSame('MMS', $messages[0]->ContentClass());
        $this->assertSame(1024, $messages[0]->Size());
        $this->assertNull($messages[0]->Recipient());
        $this->assertNull($messages[0]->SimNumber());
        $this->assertNull($messages[0]->Subject());
        $this->assertSame('mms-2', $messages[1]->MessageId());
        $this->assertSame('tx-2', $messages[1]->TransactionId());
        $this->assertSame('text', $messages[1]->ContentClass());
        $this->assertSame(0, $messages[1]->Size());
        $this->assertSame('+79990003333', $messages[1]->Recipient());
        $this->assertSame(1, $messages[1]->SimNumber());
        $this->assertSame('Hello', $messages[1]->Subject());
        $this->assertSame('mms-3', $messages[2]->MessageId());
        $this->assertSame('tx-3', $messages[2]->TransactionId());
        $this->assertSame(1048576, $messages[2]->Size());
    }

    public function testFromObjectWithMissingMessagesReturnsEmptyArray(): void {
        $batch = MmsBatchReceivedPayload::FromObject(self::decodeObject('{}'));

        $this->assertSame([], $batch->Messages());
    }

    public function testFromObjectWithNonArrayMessagesReturnsEmptyArray(): void {
        $batch = MmsBatchReceivedPayload::FromObject(self::decodeObject('{"messages":"not-an-array"}'));

        $this->assertSame([], $batch->Messages());
    }

    public function testFromObjectWithObjectMessagesReturnsEmptyArray(): void {
        $batch = MmsBatchReceivedPayload::FromObject(self::decodeObject('{"messages":{}}'));

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
