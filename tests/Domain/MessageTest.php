<?php

namespace AndroidSmsGateway\Tests\Domain;

use PHPUnit\Framework\TestCase;
use AndroidSmsGateway\Domain\Message;
use AndroidSmsGateway\Interfaces\SerializableInterface;

final class MessageTest extends TestCase {
    public function testCanBeInstantiated(): void {
        $message = new Message('Hello', ['+1234567890']);
        $this->assertInstanceOf(Message::class, $message);
    }

    public function testImplementsSerializableInterface(): void {
        $message = new Message('Hello', ['+1234567890']);
        $this->assertInstanceOf(SerializableInterface::class, $message);
    }

    public function testCanSerializeToObject(): void {
        $messageText = 'Hello';
        $phoneNumbers = ['+1234567890', '+0987654321'];
        $id = 'msg_12345';
        $ttl = 3600;
        $simNumber = 2;
        $withDeliveryReport = false;

        $message = new Message($messageText, $phoneNumbers, $id, $ttl, $simNumber, $withDeliveryReport);
        $serialized = $message->ToObject();

        $expected = (object) [
            'id' => $id,
            'message' => $messageText,
            'ttl' => $ttl,
            'simNumber' => $simNumber,
            'withDeliveryReport' => $withDeliveryReport,
            'isEncrypted' => false,
            'phoneNumbers' => $phoneNumbers
        ];

        $this->assertEquals($expected, $serialized);
    }

    public function testDefaultsWithNullParameters(): void {
        $messageText = 'Hello';
        $phoneNumbers = ['+1234567890'];

        $message = new Message($messageText, $phoneNumbers);
        $serialized = $message->ToObject();

        $this->assertNull($serialized->id);
        $this->assertNull($serialized->ttl);
        $this->assertNull($serialized->simNumber);
        $this->assertTrue($serialized->withDeliveryReport);
    }
}