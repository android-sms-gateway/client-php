<?php

namespace AndroidSmsGateway\Tests\Domain;

use AndroidSmsGateway\Enums\IncomingMessageType;
use PHPUnit\Framework\TestCase;

class IncomingMessageTypeTest extends TestCase {
    public function testConstantsRoundtrip(): void {
        $values = [
            IncomingMessageType::SMS,
            IncomingMessageType::DATA_SMS,
            IncomingMessageType::MMS,
            IncomingMessageType::MMS_DOWNLOADED,
        ];

        foreach ($values as $value) {
            $type = IncomingMessageType::FromValue($value);

            $this->assertSame($value, $type->Value());
        }
    }

    public function testFactories(): void {
        $this->assertSame(IncomingMessageType::SMS, IncomingMessageType::SMS()->Value());
        $this->assertSame(IncomingMessageType::DATA_SMS, IncomingMessageType::DATA_SMS()->Value());
        $this->assertSame(IncomingMessageType::MMS, IncomingMessageType::MMS()->Value());
        $this->assertSame(IncomingMessageType::MMS_DOWNLOADED, IncomingMessageType::MMS_DOWNLOADED()->Value());
    }

    public function testValue(): void {
        $type = IncomingMessageType::FromValue(IncomingMessageType::MMS);

        $this->assertSame(IncomingMessageType::MMS, $type->Value());
    }

    public function testToString(): void {
        $type = IncomingMessageType::FromValue(IncomingMessageType::DATA_SMS);

        $this->assertSame(IncomingMessageType::DATA_SMS, (string) $type);
    }

    public function testFromValueInvalid(): void {
        foreach (['', 'sms', 'TEXT', 'SMS '] as $value) {
            try {
                IncomingMessageType::FromValue($value);

                $this->fail('Expected InvalidArgumentException for value "' . $value . '"');
            } catch (\InvalidArgumentException $e) {
                $this->assertSame('Invalid value', $e->getMessage());
            }
        }
    }
}
