<?php

namespace AndroidSmsGateway\Tests\Domain;

use AndroidSmsGateway\Domain\MmsBatchDownloadedPayload;
use AndroidSmsGateway\Domain\MmsDownloadedAttachment;
use AndroidSmsGateway\Domain\MmsDownloadedPayload;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

final class MmsBatchDownloadedPayloadTest extends TestCase {
    public function testCanBeInstantiated(): void {
        $payload = new MmsBatchDownloadedPayload([]);
        $this->assertInstanceOf(MmsBatchDownloadedPayload::class, $payload);
    }

    public function testFromObjectMapsMessagesToInnerPayloadInstancesWithAttachments(): void {
        $json = '{"messages":['
            . '{"messageId":"mms-1","phoneNumber":"+79990001111","sender":"+79990002222","receivedAt":"2026-08-18T10:00:00Z","subject":"Hello","body":"Hello World!","attachments":['
            . '{"partId":1,"contentType":"image/jpeg","name":"photo.jpg","data":"SGVsbG8gV29ybGQh","size":1024},'
            . '{"partId":2,"contentType":"audio/amr","name":null,"data":null,"size":null}'
            . ']},'
            . '{"messageId":"mms-2","phoneNumber":"+79990003333","sender":"+79990004444","receivedAt":"2026-08-18T10:01:00Z","recipient":"+79990003333","simNumber":1,"attachments":[]},'
            . '{"messageId":"mms-3","phoneNumber":"+79990005555","sender":"+79990006666","receivedAt":"2026-08-18T10:02:00Z"}'
            . ']}';

        $batch = MmsBatchDownloadedPayload::FromObject(self::decodeObject($json));
        $messages = $batch->Messages();

        $this->assertCount(3, $messages);
        $this->assertContainsOnlyInstancesOf(MmsDownloadedPayload::class, $messages);

        $this->assertSame('mms-1', $messages[0]->MessageId());
        $this->assertSame('+79990001111', $messages[0]->PhoneNumber());
        $this->assertSame('+79990002222', $messages[0]->Sender());
        $this->assertSame('Hello', $messages[0]->Subject());
        $this->assertSame('Hello World!', $messages[0]->Body());
        $this->assertNull($messages[0]->Recipient());
        $this->assertNull($messages[0]->SimNumber());
        $attachments = $messages[0]->Attachments();
        $this->assertCount(2, $attachments);
        $this->assertContainsOnlyInstancesOf(MmsDownloadedAttachment::class, $attachments);
        $this->assertSame(1, $attachments[0]->PartId());
        $this->assertSame('image/jpeg', $attachments[0]->ContentType());
        $this->assertSame('photo.jpg', $attachments[0]->Name());
        $this->assertSame('SGVsbG8gV29ybGQh', $attachments[0]->Data());
        $this->assertSame(1024, $attachments[0]->Size());
        $this->assertSame(2, $attachments[1]->PartId());
        $this->assertSame('audio/amr', $attachments[1]->ContentType());
        $this->assertNull($attachments[1]->Name());
        $this->assertNull($attachments[1]->Data());
        $this->assertNull($attachments[1]->Size());

        $this->assertSame('mms-2', $messages[1]->MessageId());
        $this->assertSame('+79990003333', $messages[1]->Recipient());
        $this->assertSame(1, $messages[1]->SimNumber());
        $this->assertSame([], $messages[1]->Attachments());

        $this->assertSame('mms-3', $messages[2]->MessageId());
        $this->assertSame([], $messages[2]->Attachments());
        $this->assertNull($messages[2]->Subject());
        $this->assertNull($messages[2]->Body());
    }

    public function testFromObjectWithMissingMessagesReturnsEmptyArray(): void {
        $batch = MmsBatchDownloadedPayload::FromObject(self::decodeObject('{}'));

        $this->assertSame([], $batch->Messages());
    }

    public function testFromObjectWithNonArrayMessagesReturnsEmptyArray(): void {
        $batch = MmsBatchDownloadedPayload::FromObject(self::decodeObject('{"messages":"not-an-array"}'));

        $this->assertSame([], $batch->Messages());
    }

    public function testFromObjectWithObjectMessagesReturnsEmptyArray(): void {
        $batch = MmsBatchDownloadedPayload::FromObject(self::decodeObject('{"messages":{}}'));

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
