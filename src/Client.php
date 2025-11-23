<?php

namespace AndroidSmsGateway;

use AndroidSmsGateway\Domain\Message;
use AndroidSmsGateway\Domain\MessageState;
use AndroidSmsGateway\Domain\Device;
use AndroidSmsGateway\Domain\LogEntry;
use AndroidSmsGateway\Domain\Webhook;
use AndroidSmsGateway\Domain\MessagesExportRequest;
use AndroidSmsGateway\Domain\Settings;
use AndroidSmsGateway\Domain\TokenRequest;
use AndroidSmsGateway\Domain\TokenResponse;
use AndroidSmsGateway\Exceptions\HttpException;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use RuntimeException;

class Client {
    public const DEFAULT_URL = 'https://api.sms-gate.app/3rdparty/v1';
    public const USER_AGENT_TEMPLATE = 'android-sms-gateway/2.0 (client; php %s)';

    protected string $authHeader;
    protected string $baseUrl;

    protected ClientInterface $client;
    protected ?Encryptor $encryptor;

    protected RequestFactoryInterface $requestFactory;
    protected StreamFactoryInterface $streamFactory;

    public function __construct(
        ?string $login,
        string $password,
        string $serverUrl = self::DEFAULT_URL,
        ?ClientInterface $client = null,
        ?Encryptor $encryptor = null
    ) {
        if (!empty($login)) {
            $this->authHeader = 'Basic ' . base64_encode($login . ':' . $password);
        } elseif (!empty($password)) {
            $passwordOrToken = $password;
            $this->authHeader = 'Bearer ' . $passwordOrToken;
        } else {
            throw new RuntimeException('Missing credentials');
        }

        $this->baseUrl = $serverUrl;
        $this->client = $client ?? Psr18ClientDiscovery::find();
        $this->encryptor = $encryptor;

        $this->requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory = Psr17FactoryDiscovery::findStreamFactory();
    }

    /**
     * Send a message (deprecated method)
     *
     * @param Message $message
     * @param bool $skipPhoneValidation
     * @return MessageState
     */
    public function Send(Message $message, bool $skipPhoneValidation = false): MessageState {
        trigger_error(
            'The Send method is deprecated. Use the new SendMessage method instead.',
            E_USER_DEPRECATED
        );

        return $this->SendMessage($message, $skipPhoneValidation);
    }

    /**
     * Send a message
     *
     * @param Message $message
     * @param bool $skipPhoneValidation
     * @return MessageState
     */
    public function SendMessage(Message $message, bool $skipPhoneValidation = false): MessageState {
        $path = '/messages';
        $queryParams = [];
        if ($skipPhoneValidation) {
            $queryParams['skipPhoneValidation'] = 'true';
        }

        $queryString = empty($queryParams) ? '' : '?' . http_build_query($queryParams);

        if (isset($this->encryptor)) {
            $message = $message->Encrypt($this->encryptor);
        }

        $response = $this->sendRequest(
            'POST',
            $path . $queryString,
            $message
        );
        if (!is_object($response)) {
            throw new RuntimeException('Invalid response');
        }

        $state = MessageState::FromObject($response);

        if (isset($this->encryptor)) {
            $state = $state->Decrypt($this->encryptor);
        }

        return $state;
    }

    /**
     * Get message state by ID (deprecated method)
     *
     * @param string $id
     * @return MessageState
     */
    public function GetState(string $id): MessageState {
        trigger_error(
            'The GetState method is deprecated. Use the new GetMessageState method instead.',
            E_USER_DEPRECATED
        );

        return $this->GetMessageState($id);
    }

    /**
     * Get message state by ID
     *
     * @param string $id
     * @return MessageState
     */
    public function GetMessageState(string $id): MessageState {
        $path = '/messages/' . $id;

        $response = $this->sendRequest(
            'GET',
            $path
        );
        if (!is_object($response)) {
            throw new RuntimeException('Invalid response');
        }

        $state = MessageState::FromObject($response);

        if (isset($this->encryptor)) {
            $state = $state->Decrypt($this->encryptor);
        }

        return $state;
    }


    /**
     * List all devices
     *
     * @return array<Device>
     */
    public function ListDevices(): array {
        $path = '/devices';

        $response = $this->sendRequest(
            'GET',
            $path
        );
        if (!is_array($response)) {
            throw new RuntimeException('Invalid response');
        }

        return array_map(
            static fn($obj) => Device::FromObject($obj),
            $response
        );
    }

    /**
     * Remove a device by ID
     *
     * @param string $id
     * @return void
     */
    public function RemoveDevice(string $id): void {
        $path = '/devices/' . $id;

        $this->sendRequest(
            'DELETE',
            $path
        );
    }

    /**
     * Check system health
     *
     * @return object
     */
    public function HealthCheck(): object {
        $path = '/health';

        $response = $this->sendRequest(
            'GET',
            $path
        );
        if (!is_object($response)) {
            throw new RuntimeException('Invalid response');
        }

        return $response;
    }

    /**
     * Request inbox messages export
     *
     * @param MessagesExportRequest $request
     * @return object
     */
    public function RequestInboxExport(MessagesExportRequest $request): object {
        $path = '/inbox/export';

        $response = $this->sendRequest(
            'POST',
            $path,
            $request
        );
        if (!is_object($response)) {
            throw new RuntimeException('Invalid response');
        }

        return $response;
    }

    /**
     * Get logs within a specified time range
     *
     * @param string|null $from
     * @param string|null $to
     * @return array<LogEntry>
     */
    public function GetLogs(?string $from = null, ?string $to = null): array {
        $path = '/logs';
        $queryParams = [];
        if ($from !== null) {
            $queryParams['from'] = $from;
        }
        if ($to !== null) {
            $queryParams['to'] = $to;
        }

        $queryString = empty($queryParams) ? '' : '?' . http_build_query($queryParams);

        $response = $this->sendRequest(
            'GET',
            $path . $queryString
        );
        if (!is_array($response)) {
            throw new RuntimeException('Invalid response');
        }

        return array_map(
            static fn($obj) => LogEntry::FromObject($obj),
            $response
        );
    }

    /**
     * Get user settings
     *
     * @return Settings
     */
    public function GetSettings(): Settings {
        $path = '/settings';

        $response = $this->sendRequest(
            'GET',
            $path
        );
        if (!is_object($response)) {
            throw new RuntimeException('Invalid response');
        }

        return Settings::FromObject($response);
    }

    /**
     * Update user settings
     *
     * @param Settings $settings
     * @return Settings
     */
    public function ReplaceSettings(Settings $settings): Settings {
        $path = '/settings';

        $response = $this->sendRequest(
            'PUT',
            $path,
            $settings
        );
        if (!is_object($response)) {
            throw new RuntimeException('Invalid response');
        }

        return Settings::FromObject($response);
    }

    /**
     * Partially update user settings
     *
     * @param Settings $settings
     * @return Settings
     */
    public function PatchSettings(Settings $settings): Settings {
        $path = '/settings';

        $response = $this->sendRequest(
            'PATCH',
            $path,
            $settings
        );
        if (!is_object($response)) {
            throw new RuntimeException('Invalid response');
        }

        return Settings::FromObject($response);
    }

    /**
     * List all webhooks
     *
     * @return array<Webhook>
     */
    public function ListWebhooks(): array {
        $path = '/webhooks';

        $response = $this->sendRequest(
            'GET',
            $path
        );
        if (!is_array($response)) {
            throw new RuntimeException('Invalid response');
        }

        return array_map(
            static fn($obj) => Webhook::FromObject($obj),
            $response
        );
    }

    /**
     * Register a webhook
     *
     * @param Webhook $webhook
     * @return Webhook
     */
    public function RegisterWebhook(Webhook $webhook): Webhook {
        $path = '/webhooks';

        $response = $this->sendRequest(
            'POST',
            $path,
            $webhook
        );
        if (!is_object($response)) {
            throw new RuntimeException('Invalid response');
        }

        return Webhook::FromObject($response);
    }

    /**
     * Delete a webhook by ID
     *
     * @param string $id
     * @return void
     */
    public function DeleteWebhook(string $id): void {
        $path = '/webhooks/' . $id;

        $this->sendRequest(
            'DELETE',
            $path
        );
    }

    /**
     * Generate a new JWT token
     *
     * @param TokenRequest $request
     * @return TokenResponse
     */
    public function GenerateToken(TokenRequest $request): TokenResponse {
        $path = '/auth/token';

        $response = $this->sendRequest(
            'POST',
            $path,
            $request
        );
        if (!is_object($response)) {
            throw new RuntimeException('Invalid response');
        }

        return TokenResponse::FromObject($response);
    }

    /**
     * Revoke a JWT token
     *
     * @param string $jti
     * @return void
     */
    public function RevokeToken(string $jti): void {
        $path = '/auth/token/' . $jti;

        $this->sendRequest(
            'DELETE',
            $path
        );
    }

    /**
     * @param \AndroidSmsGateway\Interfaces\SerializableInterface|null $payload
     * @throws \Http\Client\Exception\HttpException
     * @throws \RuntimeException
     * @return object|array<object>|null
     */
    protected function sendRequest(string $method, string $path, $payload = null) {
        $data = isset($payload)
            ? json_encode($payload->ToObject())
            : null;
        if ($data === false) {
            throw new RuntimeException('Can\'t serialize data');
        }

        $request = $this->requestFactory
            ->createRequest(
                $method,
                $this->baseUrl . $path
            )
            ->withAddedHeader('User-Agent', sprintf(self::USER_AGENT_TEMPLATE, PHP_VERSION))
            ->withAddedHeader('Authorization', $this->authHeader);

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
            throw new RuntimeException('Can\'t parse response');
        }

        return $result;
    }
}
