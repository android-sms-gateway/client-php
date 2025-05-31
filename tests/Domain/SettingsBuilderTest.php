<?php

namespace AndroidSmsGateway\Tests\Domain;

use AndroidSmsGateway\Domain\Settings;
use AndroidSmsGateway\Domain\SettingsBuilder;
use PHPUnit\Framework\TestCase;

class SettingsBuilderTest extends TestCase {
    public function testBuildWithMinimalParameters(): void {
        $settings = (new SettingsBuilder())->build();

        $this->assertInstanceOf(Settings::class, $settings);
        $obj = $settings->ToObject();
        $this->assertObjectNotHasProperty('encryption', $obj);
    }
}