<?php

namespace AndroidSmsGateway\Tests\Domain;

use Http\Client\Curl\Client as CurlClient;
use AndroidSmsGateway\Client;
use AndroidSmsGateway\Domain\Message;
use AndroidSmsGateway\Domain\MessageState;
use AndroidSmsGateway\Domain\RecipientState;
use AndroidSmsGateway\Enums\ProcessState;
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

    public function testSend(): void {
        $messageMock = $this->createMock(Message::class);
        $messageMock->method('ToObject')->willReturn((object) []);

        $responseMock = self::mockResponse(
            '{"id":"123","state":"Sent","recipients":[{"phoneNumber":"+79000000000","state":"Sent"}]}',
            201,
            ['Content-Type' => 'application/json']
        );

        $this->mockClient->addResponse($responseMock);

        $messageState = $this->client->Send($messageMock);
        $req = $this->mockClient->getLastRequest();
        $this->assertEquals('POST', $req->getMethod());
        $this->assertEquals('/api/3rdparty/v1/message', $req->getUri()->getPath());
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

    public function testGetState(): void {
        $responseMock = self::mockResponse(
            '{"id":"123","state":"Delivered","recipients":[{"phoneNumber":"+79000000000","state":"Delivered"}]}',
            200,
            ['Content-Type' => 'application/json']
        );

        $this->mockClient->addResponse($responseMock);

        $messageState = $this->client->GetState('123');
        $req = $this->mockClient->getLastRequest();
        $this->assertEquals('GET', $req->getMethod());
        $this->assertEquals('/api/3rdparty/v1/message/123', $req->getUri()->getPath());
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

        $messageState = $client->Send($message);
        $this->assertInstanceOf(MessageState::class, $messageState);
        $this->assertEquals(ProcessState::PENDING(), $messageState->State());

        $messageState2 = $client->GetState($messageState->ID());
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

    public const MOCK_LOGIN = 'login';
    public const MOCK_PASSWORD = 'password';
}