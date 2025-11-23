<?php

namespace AndroidSmsGateway\Tests;

use AndroidSmsGateway\Client;
use AndroidSmsGateway\Domain\Message;
use AndroidSmsGateway\Domain\MessagesExportRequest;
use AndroidSmsGateway\Domain\MessageState;
use AndroidSmsGateway\Domain\Settings;
use AndroidSmsGateway\Domain\Webhook;
use AndroidSmsGateway\Domain\TokenRequest;
use AndroidSmsGateway\Domain\TokenResponse;
use AndroidSmsGateway\Enums\ProcessState;
use AndroidSmsGateway\Enums\WebhookEvent;
use Http\Client\Curl\Client as CurlClient;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Mock\Client as MockClient;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class ClientTest extends TestCase {
    private static ResponseFactoryInterface $responseFactory;
    private static StreamFactoryInterface $streamFactory;

    private Client $client;
    private MockClient $mockClient;

    public static function setUpBeforeClass(): void {
        self::$responseFactory = Psr17FactoryDiscovery::findResponseFactory();
        self::$streamFactory = Psr17FactoryDiscovery::findStreamFactory();
    }

    protected function setUp(): void {
        $this->mockClient = new MockClient(self::$responseFactory);
        $this->client = new Client(self::MOCK_LOGIN, self::MOCK_PASSWORD, Client::DEFAULT_URL, $this->mockClient);
    }

    public function testSendMessage(): void {
        $messageMock = $this->createMock(Message::class);
        $messageMock->method('ToObject')->willReturn((object) []);

        $responseMock = self::mockResponse(
            '{"id":"123","state":"Sent","recipients":[{"phoneNumber":"+79000000000","state":"Sent"}]}',
            201,
            ['Content-Type' => 'application/json']
        );

        $this->mockClient->addResponse($responseMock);

        $messageState = $this->client->SendMessage($messageMock);
        $req = $this->mockClient->getLastRequest();
        $this->assertEquals('POST', $req->getMethod());
        $this->assertEquals('/3rdparty/v1/messages', $req->getUri()->getPath());
        $this->assertEquals(
            'Basic ' . base64_encode(self::MOCK_LOGIN . ':' . self::MOCK_PASSWORD),
            $req->getHeaderLine('Authorization')
        );
        $this->assertEquals(
            'application/json',
            $req->getHeaderLine('Content-Type')
        );
        $this->assertEquals(
            sprintf(Client::USER_AGENT_TEMPLATE, PHP_VERSION),
            $req->getHeaderLine('User-Agent')
        );


        $this->assertInstanceOf(MessageState::class, $messageState);
    }

    public function testGetMessageState(): void {
        $responseMock = self::mockResponse(
            '{"id":"123","state":"Delivered","recipients":[{"phoneNumber":"+79000000000","state":"Delivered"}]}',
            200,
            ['Content-Type' => 'application/json']
        );

        $this->mockClient->addResponse($responseMock);

        $messageState = $this->client->GetMessageState('123');
        $req = $this->mockClient->getLastRequest();
        $this->assertEquals('GET', $req->getMethod());
        $this->assertEquals('/3rdparty/v1/messages/123', $req->getUri()->getPath());
        $this->assertEquals(
            'Basic ' . base64_encode(self::MOCK_LOGIN . ':' . self::MOCK_PASSWORD),
            $req->getHeaderLine('Authorization')
        );

        $this->assertInstanceOf(MessageState::class, $messageState);
    }

    public function testServer(): void {
        $login = getenv('TEST_LOGIN');
        $password = getenv('TEST_PASSWORD');
        $phoneNumber = getenv('TEST_PHONE_NUMBER');
        if (empty($login) || empty($password) || empty($phoneNumber)) {
            $this->markTestSkipped();
        }

        $curlClient = new CurlClient(
            null,
            null,
            [
                CURLOPT_TIMEOUT => 1,
            ]
        );
        $client = new Client($login, $password, Client::DEFAULT_URL, $curlClient);

        $message = new Message(date('Y-m-d H:i:s'), [$phoneNumber]);

        $messageState = $client->SendMessage($message);
        $this->assertInstanceOf(MessageState::class, $messageState);
        $this->assertEquals(ProcessState::PENDING(), $messageState->State());

        $messageState2 = $client->GetMessageState($messageState->ID());
        $this->assertInstanceOf(MessageState::class, $messageState2);
        $this->assertEquals($messageState->ID(), $messageState2->ID());
    }

    /**
     * @param array<string, string> $headers
     * @return ResponseInterface
     */
    private static function mockResponse(string $body, int $code = 200, array $headers = ['Content-Type' => 'application/json']): ResponseInterface {
        $response = self::$responseFactory
            ->createResponse($code)
            ->withBody(self::$streamFactory->createStream($body));

        foreach ($headers as $key => $value) {
            $response = $response->withAddedHeader(
                $key,
                $value
            );
        }

        return $response;
    }

    public function testListDevices(): void {
        $responseMock = self::mockResponse(
            '[{"id":"123","name":"Test Device","createdAt":"2020-01-01T00:00:00Z","updatedAt":"2020-01-01T00:00:00Z"}]',
            200,
            ['Content-Type' => 'application/json']
        );

        $this->mockClient->addResponse($responseMock);

        $devices = $this->client->ListDevices();
        $req = $this->mockClient->getLastRequest();
        $this->assertEquals('GET', $req->getMethod());
        $this->assertEquals('/3rdparty/v1/devices', $req->getUri()->getPath());
        $this->assertEquals(
            'Basic ' . base64_encode(self::MOCK_LOGIN . ':' . self::MOCK_PASSWORD),
            $req->getHeaderLine('Authorization')
        );

        $this->assertCount(1, $devices);
        $this->assertEquals('123', $devices[0]->ID());
    }

    public function testRemoveDevice(): void {
        $responseMock = self::mockResponse('', 204);

        $this->mockClient->addResponse($responseMock);

        $this->client->RemoveDevice('123');
        $req = $this->mockClient->getLastRequest();
        $this->assertEquals('DELETE', $req->getMethod());
        $this->assertEquals('/3rdparty/v1/devices/123', $req->getUri()->getPath());
        $this->assertEquals(
            'Basic ' . base64_encode(self::MOCK_LOGIN . ':' . self::MOCK_PASSWORD),
            $req->getHeaderLine('Authorization')
        );
    }

    public function testHealthCheck(): void {
        $responseMock = self::mockResponse(
            '{"status":"pass","checks":{},"releaseId":1,"version":"1.0.0"}',
            200,
            ['Content-Type' => 'application/json']
        );

        $this->mockClient->addResponse($responseMock);

        $health = $this->client->HealthCheck();
        $req = $this->mockClient->getLastRequest();
        $this->assertEquals('GET', $req->getMethod());
        $this->assertEquals('/3rdparty/v1/health', $req->getUri()->getPath());
        $this->assertEquals(
            'Basic ' . base64_encode(self::MOCK_LOGIN . ':' . self::MOCK_PASSWORD),
            $req->getHeaderLine('Authorization')
        );

        $this->assertEquals('pass', $health->status);
    }

    public function testRequestInboxExport(): void {
        $request = new MessagesExportRequest('123', '2020-01-01T00:00:00Z', '2020-01-02T00:00:00Z');

        $responseMock = self::mockResponse(
            '{"status":"accepted"}',
            202,
            ['Content-Type' => 'application/json']
        );

        $this->mockClient->addResponse($responseMock);

        $result = $this->client->RequestInboxExport($request);
        $req = $this->mockClient->getLastRequest();
        $this->assertEquals('POST', $req->getMethod());
        $this->assertEquals('/3rdparty/v1/inbox/export', $req->getUri()->getPath());
        $this->assertEquals(
            'Basic ' . base64_encode(self::MOCK_LOGIN . ':' . self::MOCK_PASSWORD),
            $req->getHeaderLine('Authorization')
        );

        $this->assertEquals('accepted', $result->status);
    }

    public function testGetLogs(): void {
        $responseMock = self::mockResponse(
            '[{"id":1,"message":"Test log","module":"test","priority":"INFO","createdAt":"2020-01-01T00:00:00Z"}]',
            200,
            ['Content-Type' => 'application/json']
        );

        $this->mockClient->addResponse($responseMock);

        $logs = $this->client->GetLogs('2020-01-01T00:00:00Z', '2020-01-02T00:00:00Z');
        $req = $this->mockClient->getLastRequest();
        $this->assertEquals('GET', $req->getMethod());
        $uri = $req->getUri()->getPath();
        $this->assertEquals('/3rdparty/v1/logs', $uri);
        $query = $req->getUri()->getQuery();
        $this->assertStringContainsString('from=2020-01-01T00%3A00%3A00Z', $query);
        $this->assertStringContainsString('to=2020-01-02T00%3A00%3A00Z', $query);
        $this->assertEquals(
            'Basic ' . base64_encode(self::MOCK_LOGIN . ':' . self::MOCK_PASSWORD),
            $req->getHeaderLine('Authorization')
        );

        $this->assertCount(1, $logs);
        $this->assertEquals(1, $logs[0]->ID());
    }

    public function testGetSettings(): void {
        $responseMock = self::mockResponse(
            '{"encryption":{},"gateway":{},"logs":{},"messages":{},"ping":{},"webhooks":{}}',
            200,
            ['Content-Type' => 'application/json']
        );

        $this->mockClient->addResponse($responseMock);

        $settings = $this->client->GetSettings();
        $req = $this->mockClient->getLastRequest();
        $this->assertEquals('GET', $req->getMethod());
        $this->assertEquals('/3rdparty/v1/settings', $req->getUri()->getPath());
        $this->assertEquals(
            'Basic ' . base64_encode(self::MOCK_LOGIN . ':' . self::MOCK_PASSWORD),
            $req->getHeaderLine('Authorization')
        );

        $this->assertObjectHasProperty('encryption', $settings);
    }

    public function testUpdateSettings(): void {
        $settings = new Settings(
            new \stdClass(),
            new \stdClass(),
            new \stdClass(),
            new \stdClass(),
            new \stdClass(),
            new \stdClass()
        );

        $responseMock = self::mockResponse(
            '{"logs":{"lifetime_days":1}}',
            200,
            ['Content-Type' => 'application/json']
        );

        $this->mockClient->addResponse($responseMock);

        $result = $this->client->ReplaceSettings($settings);
        $req = $this->mockClient->getLastRequest();
        $this->assertEquals('PUT', $req->getMethod());
        $this->assertEquals('/3rdparty/v1/settings', $req->getUri()->getPath());
        $this->assertEquals(
            'Basic ' . base64_encode(self::MOCK_LOGIN . ':' . self::MOCK_PASSWORD),
            $req->getHeaderLine('Authorization')
        );

        $this->assertEquals(1, $result->Logs()->lifetime_days);
    }

    public function testPatchSettings(): void {
        $settings = new Settings(
            new \stdClass(),
            null,
            null,
            null,
            null,
            null
        );

        $responseMock = self::mockResponse(
            '{"logs":{"lifetime_days":1}}',
            200,
            ['Content-Type' => 'application/json']
        );

        $this->mockClient->addResponse($responseMock);

        $result = $this->client->PatchSettings($settings);
        $req = $this->mockClient->getLastRequest();
        $this->assertEquals('PATCH', $req->getMethod());
        $this->assertEquals('/3rdparty/v1/settings', $req->getUri()->getPath());
        $this->assertEquals(
            'Basic ' . base64_encode(self::MOCK_LOGIN . ':' . self::MOCK_PASSWORD),
            $req->getHeaderLine('Authorization')
        );

        $this->assertEquals(1, $result->Logs()->lifetime_days);
    }

    public function testListWebhooks(): void {
        $responseMock = self::mockResponse(
            '[{"id":"123","event":"sms:received","url":"https://example.com/webhook"}]',
            200,
            ['Content-Type' => 'application/json']
        );

        $this->mockClient->addResponse($responseMock);

        $webhooks = $this->client->ListWebhooks();
        $req = $this->mockClient->getLastRequest();
        $this->assertEquals('GET', $req->getMethod());
        $this->assertEquals('/3rdparty/v1/webhooks', $req->getUri()->getPath());
        $this->assertEquals(
            'Basic ' . base64_encode(self::MOCK_LOGIN . ':' . self::MOCK_PASSWORD),
            $req->getHeaderLine('Authorization')
        );

        $this->assertCount(1, $webhooks);
        $this->assertEquals('123', $webhooks[0]->ID());
    }

    public function testRegisterWebhook(): void {
        $webhook = new Webhook(WebhookEvent::SMS_RECEIVED(), 'https://example.com/webhook');

        $responseMock = self::mockResponse(
            '{"id":"123","event":"sms:received","url":"https://example.com/webhook"}',
            201,
            ['Content-Type' => 'application/json']
        );

        $this->mockClient->addResponse($responseMock);

        $result = $this->client->RegisterWebhook($webhook);
        $req = $this->mockClient->getLastRequest();
        $this->assertEquals('POST', $req->getMethod());
        $this->assertEquals('/3rdparty/v1/webhooks', $req->getUri()->getPath());
        $this->assertEquals(
            'Basic ' . base64_encode(self::MOCK_LOGIN . ':' . self::MOCK_PASSWORD),
            $req->getHeaderLine('Authorization')
        );

        $this->assertEquals('123', $result->ID());
    }

    public function testDeleteWebhook(): void {
        $responseMock = self::mockResponse('', 204);

        $this->mockClient->addResponse($responseMock);

        $this->client->DeleteWebhook('123');
        $req = $this->mockClient->getLastRequest();
        $this->assertEquals('DELETE', $req->getMethod());
        $this->assertEquals('/3rdparty/v1/webhooks/123', $req->getUri()->getPath());
        $this->assertEquals(
            'Basic ' . base64_encode(self::MOCK_LOGIN . ':' . self::MOCK_PASSWORD),
            $req->getHeaderLine('Authorization')
        );
    }

    public function testGenerateToken(): void {
        $tokenRequest = new TokenRequest(['read', 'write'], 3600);

        $responseMock = self::mockResponse(
            '{"access_token":"test-token","token_type":"Bearer","id":"token-id","expires_at":"2023-12-31T23:59:59Z"}',
            201,
            ['Content-Type' => 'application/json']
        );

        $this->mockClient->addResponse($responseMock);

        $tokenResponse = $this->client->GenerateToken($tokenRequest);
        $req = $this->mockClient->getLastRequest();
        $this->assertEquals('POST', $req->getMethod());
        $this->assertEquals('/3rdparty/v1/auth/token', $req->getUri()->getPath());
        $this->assertEquals(
            'Basic ' . base64_encode(self::MOCK_LOGIN . ':' . self::MOCK_PASSWORD),
            $req->getHeaderLine('Authorization')
        );
        $this->assertEquals(
            'application/json',
            $req->getHeaderLine('Content-Type')
        );

        $this->assertInstanceOf(TokenResponse::class, $tokenResponse);
        $this->assertEquals('test-token', $tokenResponse->AccessToken());
        $this->assertEquals('Bearer', $tokenResponse->TokenType());
        $this->assertEquals('token-id', $tokenResponse->ID());
        $this->assertEquals('2023-12-31T23:59:59Z', $tokenResponse->ExpiresAt());
    }

    public function testRevokeToken(): void {
        $responseMock = self::mockResponse('', 204);

        $this->mockClient->addResponse($responseMock);

        $this->client->RevokeToken('token-id');
        $req = $this->mockClient->getLastRequest();
        $this->assertEquals('DELETE', $req->getMethod());
        $this->assertEquals('/3rdparty/v1/auth/token/token-id', $req->getUri()->getPath());
        $this->assertEquals(
            'Basic ' . base64_encode(self::MOCK_LOGIN . ':' . self::MOCK_PASSWORD),
            $req->getHeaderLine('Authorization')
        );
    }

    public function testClientWithJwtToken(): void {
        $jwtToken = 'test-jwt-token';
        $client = new Client(null, $jwtToken, Client::DEFAULT_URL, $this->mockClient);

        $responseMock = self::mockResponse(
            '{"id":"123","state":"Sent","recipients":[{"phoneNumber":"+79000000000","state":"Sent"}]}',
            201,
            ['Content-Type' => 'application/json']
        );

        $this->mockClient->addResponse($responseMock);

        $messageMock = $this->createMock(Message::class);
        $messageMock->method('ToObject')->willReturn((object) []);

        $client->SendMessage($messageMock);
        $req = $this->mockClient->getLastRequest();
        $this->assertEquals('Bearer ' . $jwtToken, $req->getHeaderLine('Authorization'));
    }

    public const MOCK_LOGIN = 'login';
    public const MOCK_PASSWORD = 'password';
}