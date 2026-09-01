<?php

namespace AndroidSmsGateway\Tests\Domain;

use PHPUnit\Framework\TestCase;
use AndroidSmsGateway\Domain\Message;
use AndroidSmsGateway\Domain\MmsAttachment;
use AndroidSmsGateway\Domain\MmsMessage;
use AndroidSmsGateway\Encryptor;
use AndroidSmsGateway\Interfaces\SerializableInterface;

final class MessageTest extends TestCase {
    private const MMS_FIXTURE = '{"subject":"Hello","text":"World","attachments":[{"contentType":"image/png","name":"picture.png","data":"BASE64DATA"}]}';

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
        $this->assertNull($serialized->simNumber);
        $this->assertTrue($serialized->withDeliveryReport);
        $this->assertFalse($serialized->isEncrypted);
    }

    public function testMmsMessageToObjectMatchesLockedFixtureByteExact(): void {
        $mms = new MmsMessage('Hello', 'World', [new MmsAttachment('image/png', 'BASE64DATA', 'picture.png')]);

        $this->assertSame(self::MMS_FIXTURE, json_encode($mms->ToObject(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));
    }

    public function testMmsAttachmentToObjectOmitsNullName(): void {
        $attachment = new MmsAttachment('image/png', 'BASE64DATA');

        $this->assertSame(
            '{"contentType":"image/png","data":"BASE64DATA"}',
            json_encode($attachment->ToObject(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES)
        );
    }

    public function testMmsMessageOmitsNullSubjectTextAndEmptyAttachments(): void {
        $mms = new MmsMessage();

        $this->assertSame('{}', json_encode($mms->ToObject(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));
    }

    public function testMmsMessageOmitsSubjectOnlyWhenNull(): void {
        $mms = new MmsMessage(null, 'World', [new MmsAttachment('image/png', 'BASE64DATA')]);

        $this->assertSame(
            '{"text":"World","attachments":[{"contentType":"image/png","data":"BASE64DATA"}]}',
            json_encode($mms->ToObject(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES)
        );
    }

    public function testMessageSerializesMmsMessageWithoutMessageKey(): void {
        $mms = new MmsMessage('Hello', 'World', [new MmsAttachment('image/png', 'BASE64DATA', 'picture.png')]);
        $message = new Message($mms, ['+1234567890'], null, null, null, true, null, null);

        $json = json_encode($message->ToObject(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);

        $this->assertStringNotContainsString('"message"', $json);
        $this->assertSame(
            '{"id":null,"simNumber":null,"withDeliveryReport":true,"isEncrypted":false,"phoneNumbers":["+1234567890"],"mmsMessage":' . self::MMS_FIXTURE . '}',
            $json
        );
    }

    public function testLegacyTextMessageStillSerializesMessageKey(): void {
        $message = new Message('Hello', ['+1234567890']);

        $json = json_encode($message->ToObject(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);

        $this->assertSame(
            '{"id":null,"simNumber":null,"withDeliveryReport":true,"isEncrypted":false,"phoneNumbers":["+1234567890"],"message":"Hello"}',
            $json
        );
    }

    public function testMessageMmsMessageDecodesToFixtureShape(): void {
        $mms = new MmsMessage('Hello', 'World', [new MmsAttachment('image/png', 'BASE64DATA', 'picture.png')]);
        $message = new Message($mms, ['+1234567890'], null, null, null, true, null, null);

        $json = json_encode($message->ToObject(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);

        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($json, true);

        $this->assertSame([
            'subject' => 'Hello',
            'text' => 'World',
            'attachments' => [
                ['contentType' => 'image/png', 'name' => 'picture.png', 'data' => 'BASE64DATA'],
            ],
        ], $decoded['mmsMessage']);
    }

    public function testMessageOmitsMmsMessageWhenNotSet(): void {
        $message = new Message('Hello', ['+1234567890']);

        $json = json_encode($message->ToObject(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);

        $this->assertStringNotContainsString('mmsMessage', $json);
    }

    public function testEncryptEncryptsMmsMessageFields(): void {
        $encryptor = new Encryptor('passphrase');
        $mms = new MmsMessage('Hello', 'World', [new MmsAttachment('image/png', 'BASE64DATA', 'picture.png')]);
        $message = new Message($mms, ['+1234567890'], null, null, null, true, null, null);

        $message->Encrypt($encryptor);

        /** @var MmsMessage $encryptedMms */
        $encryptedMms = $message->MmsMessage();

        $this->assertNotSame('Hello', $encryptedMms->Subject());
        $this->assertNotSame('World', $encryptedMms->Text());
        $this->assertNotSame('BASE64DATA', $encryptedMms->Attachments()[0]->Data());
        $this->assertNotSame('picture.png', $encryptedMms->Attachments()[0]->Name());
        $this->assertSame('image/png', $encryptedMms->Attachments()[0]->ContentType());

        $this->assertSame('Hello', $encryptor->Decrypt($encryptedMms->Subject()));
        $this->assertSame('World', $encryptor->Decrypt($encryptedMms->Text()));
        $this->assertSame('BASE64DATA', $encryptor->Decrypt($encryptedMms->Attachments()[0]->Data()));
        $this->assertSame('picture.png', $encryptor->Decrypt($encryptedMms->Attachments()[0]->Name()));
    }

    public function testEncryptIsIdempotentForMmsMessage(): void {
        $encryptor = new Encryptor('passphrase');
        $mms = new MmsMessage('Hello', 'World', [new MmsAttachment('image/png', 'BASE64DATA')]);
        $message = new Message($mms, ['+1234567890'], null, null, null, true, null, null);

        $message->Encrypt($encryptor);

        /** @var MmsMessage $firstPass */
        $firstPass = $message->MmsMessage();
        $subjectAfterFirstPass = $firstPass->Subject();

        $message->Encrypt($encryptor);

        /** @var MmsMessage $secondPass */
        $secondPass = $message->MmsMessage();
        $this->assertSame($subjectAfterFirstPass, $secondPass->Subject());
    }

    public function testEncryptWithoutMmsMessageStillEncryptsText(): void {
        $encryptor = new Encryptor('passphrase');
        $message = new Message('Hello', ['+1234567890']);

        $message->Encrypt($encryptor);

        $this->assertTrue($message->ToObject()->isEncrypted);
        $this->assertNotSame('Hello', $message->ToObject()->message);
    }
}
