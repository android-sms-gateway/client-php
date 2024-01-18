<?php

namespace AndroidSmsGateway;

use Http\Client\Exception\HttpException;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use AndroidSmsGateway\Domain\Message;
use AndroidSmsGateway\Domain\MessageState;

class Client {
    public const DEFAULT_URL = 'https://sms.capcom.me/api/3rdparty/v1';
    public const USER_AGENT = 'android-sms-gateway/1.0 (client; php)';

    protected string $basicAuth;
    protected string $baseUrl;

    protected HttpClient $client;
    protected ?Encryptor $encryptor;

    protected RequestFactoryInterface $requestFactory;
    protected StreamFactoryInterface $streamFactory;

    public function __construct(
        string $login,
        string $password,
        string $serverUrl = self::DEFAULT_URL,
        ?HttpClient $client = null,
        ?Encryptor $encryptor = null
    ) {
        $this->basicAuth = base64_encode($login . ':' . $password);
        $this->baseUrl = $serverUrl;
        $this->client = $client ?? HttpClientDiscovery::find();
        $this->encryptor = $encryptor;

        $this->requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory = Psr17FactoryDiscovery::findStreamFactory();
    }

    public function Send(Message $message): MessageState {
        $path = '/message';

        if (isset($this->encryptor)) {
            $message = $message->Encrypt($this->encryptor);
        }

        $response = $this->sendRequest(
            'POST',
            $path,
            $message
        );
        if (!is_object($response)) {
            throw new \RuntimeException('Invalid response');
        }

        $state = MessageState::FromObject($response);

        if (isset($this->encryptor)) {
            $state = $state->Decrypt($this->encryptor);
        }

        return $state;
    }

    public function GetState(string $id): MessageState {
        $path = '/message/' . $id;

        $response = $this->sendRequest(
            'GET',
            $path
        );
        if (!is_object($response)) {
            throw new \RuntimeException('Invalid response');
        }

        $state = MessageState::FromObject($response);

        if (isset($this->encryptor)) {
            $state = $state->Decrypt($this->encryptor);
        }

        return $state;
    }

    /**
     * @param \AndroidSmsGateway\Interfaces\SerializableInterface|null $payload
     * @throws HttpException
     * @throws \RuntimeException
     * @return object|array<object>|null
     */
    protected function sendRequest(string $method, string $path, $payload = null) {
        $data = isset($payload)
            ? json_encode($payload->ToObject())
            : null;
        if ($data === false) {
            throw new \RuntimeException('Can\'t serialize data');
        }

        $request = $this->requestFactory
            ->createRequest(
                $method,
                $this->baseUrl . $path
            )
            ->withAddedHeader('Authorization', 'Basic ' . $this->basicAuth)
            ->withAddedHeader('User-Agent', self::USER_AGENT);
        if (isset($data)) {
            $request = $request
                ->withAddedHeader('Content-Type', 'application/json')
                ->withBody($this->streamFactory->createStream($data));
        }

        $response = $this->client->sendRequest($request);

        if ($response->getStatusCode() >= 400) {
            throw HttpException::create($request, $response);
        }

        $result = json_decode($response->getBody());
        if ($result === false) {
            throw new \RuntimeException('Can\'t parse response');
        }

        return $result;
    }
}