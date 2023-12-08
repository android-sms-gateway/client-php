<?php

namespace AndroidSmsGateway\Tests\Domain;

use PHPUnit\Framework\TestCase;
use AndroidSmsGateway\Domain\MessageState;
use AndroidSmsGateway\Domain\RecipientState;
use AndroidSmsGateway\Enums\ProcessState;

final class MessageStateTest extends TestCase {
    private ProcessState $processStateMock;
    private RecipientState $recipientStateMock;

    protected function setUp(): void {
        parent::setUp();
        // Create a mock for the ProcessState class.
        $this->processStateMock = ProcessState::PENDING();
        // Create a mock for the RecipientState class.
        $this->recipientStateMock = new RecipientState('+1234567890', $this->processStateMock);
    }

    public function testCanBeInstantiated(): void {
        $messageState = new MessageState('msg_123', $this->processStateMock, [$this->recipientStateMock]);
        $this->assertInstanceOf(MessageState::class, $messageState);
    }

    public function testCanGetId(): void {
        $id = 'msg_123';
        $messageState = new MessageState($id, $this->processStateMock, [$this->recipientStateMock]);
        $this->assertEquals($id, $messageState->ID());
    }

    public function testCanGetState(): void {
        $messageState = new MessageState('msg_123', $this->processStateMock, [$this->recipientStateMock]);
        $this->assertSame($this->processStateMock, $messageState->State());
    }

    public function testCanGetRecipients(): void {
        $recipients = [$this->recipientStateMock];
        $messageState = new MessageState('msg_123', $this->processStateMock, $recipients);
        $this->assertSame($recipients, $messageState->Recipients());
    }

    public function testCanCreateFromObject(): void {
        $id = 'msg_123';
        $obj = (object) [
            'id' => $id,
            'state' => ProcessState::PENDING,
            'recipients' => [
                (object) [
                    'phoneNumber' => '+1234567890',
                    'state' => ProcessState::PENDING,
                ],
            ]
        ];

        $messageState = MessageState::FromObject($obj);

        $this->assertInstanceOf(MessageState::class, $messageState);
        $this->assertEquals($id, $messageState->ID());
        $this->assertEquals(ProcessState::PENDING(), $messageState->State());
        $this->assertCount(1, $messageState->Recipients());
    }
}