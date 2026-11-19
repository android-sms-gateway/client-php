<?php

namespace AndroidSmsGateway\Tests\Domain;

use AndroidSmsGateway\Domain\Device;
use AndroidSmsGateway\Enums\DeviceState;
use PHPUnit\Framework\TestCase;

class DeviceStateTest extends TestCase {
    public function testConstantsRoundtrip(): void {
        foreach ([DeviceState::ACTIVE, DeviceState::DISABLED] as $value) {
            $this->assertSame($value, DeviceState::FromValue($value)->Value());
        }
    }

    public function testFactories(): void {
        $this->assertSame(DeviceState::ACTIVE, DeviceState::ACTIVE()->Value());
        $this->assertSame(DeviceState::DISABLED, DeviceState::DISABLED()->Value());
    }

    public function testFromValueInvalid(): void {
        $this->expectException(\InvalidArgumentException::class);

        DeviceState::FromValue('Unknown');
    }

    public function testDeviceStateParsing(): void {
        $payload = (object) [
            'id' => '123',
            'name' => 'Test Device',
            'createdAt' => '2020-01-01T00:00:00Z',
            'updatedAt' => '2020-01-01T00:00:00Z',
            'state' => DeviceState::DISABLED,
        ];

        $device = Device::FromObject($payload);

        $this->assertSame(DeviceState::DISABLED, $device->State()->Value());
        $this->assertSame(DeviceState::DISABLED, $device->ToObject()->state);

        $withoutState = Device::FromObject((object) [
            'id' => '123',
            'name' => 'Test Device',
            'createdAt' => '2020-01-01T00:00:00Z',
            'updatedAt' => '2020-01-01T00:00:00Z',
        ]);

        $this->assertNull($withoutState->State());
    }
}
