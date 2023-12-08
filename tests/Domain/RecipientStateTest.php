<?php

namespace AndroidSmsGateway\Tests\Domain;

use AndroidSmsGateway\Domain\RecipientState;
use AndroidSmsGateway\Enums\ProcessState;
use PHPUnit\Framework\TestCase;

final class RecipientStateTest extends TestCase {
    private ProcessState $processStateMock;

    protected function setUp(): void {
        parent::setUp();
        // Create a mock for the ProcessState class.
        $this->processStateMock = ProcessState::FAILED();
    }

    public function testCanBeInstantiated(): void {
        $recipientState = new RecipientState('1234567890', $this->processStateMock);
        $this->assertInstanceOf(RecipientState::class, $recipientState);
    }

    public function testCanGetPhoneNumber(): void {
        $phoneNumber = '1234567890';
        $recipientState = new RecipientState($phoneNumber, $this->processStateMock);
        $this->assertEquals($phoneNumber, $recipientState->PhoneNumber());
    }

    public function testCanGetState(): void {
        $recipientState = new RecipientState('1234567890', $this->processStateMock);
        $this->assertSame($this->processStateMock, $recipientState->State());
    }

    public function testCanGetError(): void {
        $error = 'Invalid number';
        $recipientState = new RecipientState('1234567890', $this->processStateMock, $error);
        $this->assertEquals($error, $recipientState->Error());
    }

    public function testErrorIsNullWhenNotProvided(): void {
        $recipientState = new RecipientState('1234567890', $this->processStateMock);
        $this->assertNull($recipientState->Error());
    }

    public function testCanCreateFromObject(): void {
        $phoneNumber = '1234567890';
        $error = 'Invalid number';
        $obj = (object) [
            'phoneNumber' => $phoneNumber,
            'state' => $this->processStateMock->Value(),
            'error' => $error
        ];

        $recipientState = RecipientState::FromObject($obj);

        $this->assertInstanceOf(RecipientState::class, $recipientState);
        $this->assertEquals($phoneNumber, $recipientState->PhoneNumber());
        $this->assertEquals($this->processStateMock, $recipientState->State());
        $this->assertEquals($error, $recipientState->Error());
    }
}