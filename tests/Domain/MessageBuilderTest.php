<?php

namespace AndroidSmsGateway\Tests\Domain;

use AndroidSmsGateway\Domain\Message;
use AndroidSmsGateway\Domain\MessageBuilder;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class MessageBuilderTest extends TestCase {
    public function testBuildWithMinimalParameters(): void {
        $phoneNumbers = ['+1234567890'];
        $message = (new MessageBuilder('Test message', $phoneNumbers))->build();

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals('Test message', $message->ToObject()->message);
        $this->assertEquals($phoneNumbers, $message->ToObject()->phoneNumbers);
        $this->assertTrue($message->ToObject()->withDeliveryReport);
        $this->assertFalse($message->ToObject()->isEncrypted);
    }

    public function testBuildWithAllParameters(): void {
        $phoneNumbers = ['+1234567890'];
        $message = (new MessageBuilder('Test message', $phoneNumbers))
            ->setId('123')
            ->setTtl(3600)
            ->setSimNumber(1)
            ->setWithDeliveryReport(false)
            ->setPriority(1)
            ->build();

        $obj = $message->ToObject();
        $this->assertEquals('123', $obj->id);
        $this->assertEquals(3600, $obj->ttl);
        $this->assertEquals(1, $obj->simNumber);
        $this->assertFalse($obj->withDeliveryReport);
        $this->assertEquals(1, $obj->priority);
    }

    public function testBuildWithInvalidParameters(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('validUntil and ttl cannot be set at the same time');

        $phoneNumbers = ['+1234567890'];
        (new MessageBuilder('Test message', $phoneNumbers))
            ->setTtl(3600)
            ->setValidUntil('2025-12-31T23:59:59Z')
            ->build();
    }

    public function testMethodChaining(): void {
        $phoneNumbers = ['+1234567890'];
        $builder = new MessageBuilder('Test message', $phoneNumbers);

        $this->assertInstanceOf(MessageBuilder::class, $builder->setTtl(3600));
        $this->assertInstanceOf(MessageBuilder::class, $builder->setSimNumber(1));
        $this->assertInstanceOf(MessageBuilder::class, $builder->setWithDeliveryReport(false));
    }
}