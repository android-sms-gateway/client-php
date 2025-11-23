# 📱 SMS Gateway for Android™ PHP API Client

[![License](https://img.shields.io/badge/License-Apache%202.0-blue.svg?style=for-the-badge)](https://opensource.org/licenses/Apache-2.0)
[![Latest Stable Version](https://img.shields.io/packagist/v/capcom6/android-sms-gateway.svg?style=for-the-badge)](https://packagist.org/packages/capcom6/android-sms-gateway)
[![PHP Version Require](https://img.shields.io/packagist/php-v/capcom6/android-sms-gateway?style=for-the-badge)](https://packagist.org/packages/capcom6/android-sms-gateway)
[![Total Downloads](https://img.shields.io/packagist/dt/capcom6/android-sms-gateway.svg?style=for-the-badge)](https://packagist.org/packages/capcom6/android-sms-gateway)

A modern PHP client for seamless integration with the [SMSGate](https://sms-gate.app) API. Send SMS messages, manage devices, and configure webhooks through your PHP applications with this intuitive library.

## 🔖 Table of Contents

- [📱 SMS Gateway for Android™ PHP API Client](#-sms-gateway-for-android-php-api-client)
  - [🔖 Table of Contents](#-table-of-contents)
  - [✨ Features](#-features)
  - [⚙️ Prerequisites](#️-prerequisites)
  - [📦 Installation](#-installation)
  - [🚀 Quickstart](#-quickstart)
    - [Sending an SMS](#sending-an-sms)
    - [Managing Devices](#managing-devices)
  - [🔐 Authentication](#-authentication)
    - [Basic Authentication](#basic-authentication)
    - [JWT Authentication](#jwt-authentication)
      - [Generating a JWT Token](#generating-a-jwt-token)
      - [Using a JWT Token](#using-a-jwt-token)
      - [Revoking a JWT Token](#revoking-a-jwt-token)
  - [📚 Full API Reference](#-full-api-reference)
    - [Client Initialization](#client-initialization)
      - [Basic Authentication](#basic-authentication-1)
    - [Core Methods](#core-methods)
    - [Builder Methods](#builder-methods)
  - [🔒 Security Notes](#-security-notes)
    - [Best Practices](#best-practices)
    - [Encryption Support](#encryption-support)
  - [👥 Contributing](#-contributing)
    - [Development Setup](#development-setup)
  - [📄 License](#-license)

## ✨ Features

- **Builder Pattern**: Fluent interface for message and settings configuration
- **PSR Standards**: Compatible with any PSR-18 HTTP client
- **Comprehensive API**: Access to all SMS Gateway endpoints
- **Error Handling**: Structured exception management
- **Type Safety**: Strict typing throughout the codebase
- **Encryption Support**: End-to-end message encryption
- **Dual Authentication**: Support for both Basic and JWT authentication
- **Token Management**: Generate, use, and revoke JWT tokens with configurable scopes and TTL

## ⚙️ Prerequisites

- PHP 7.4+
- [Composer](https://getcomposer.org/)
- PSR-18 compatible HTTP client (e.g., [Guzzle](https://github.com/guzzle/guzzle))
- SMS Gateway for Android account

## 📦 Installation

```bash
composer require capcom6/android-sms-gateway
```

## 🚀 Quickstart

### Sending an SMS
```php
<?php

require 'vendor/autoload.php';

use AndroidSmsGateway\Client;
use AndroidSmsGateway\Domain\MessageBuilder;

// Initialize client with credentials
$client = new Client('your_login', 'your_password');

// Build message with fluent interface
$message = (new MessageBuilder('Your message text here.', ['+1234567890']))
    ->setTtl(3600)                  // Message time-to-live in seconds
    ->setSimNumber(1)               // Use SIM slot 1
    ->setWithDeliveryReport(true)   // Request delivery report
    ->setPriority(100)              // Higher priority message
    ->build();

// Send message
try {
    $messageState = $client->SendMessage($message);
    echo "✅ Message sent! ID: " . $messageState->ID() . PHP_EOL;
    
    // Check status after delay
    sleep(5);
    $updatedState = $client->GetMessageState($messageState->ID());
    echo "📊 Message status: " . $updatedState->State() . PHP_EOL;
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
```

### Managing Devices

```php
// List registered devices
$devices = $client->ListDevices();
echo "📱 Registered devices: " . count($devices) . PHP_EOL;

// Remove a device
try {
    $client->RemoveDevice('device-id-123');
    echo "🗑️ Device removed successfully" . PHP_EOL;
} catch (\Exception $e) {
    echo "❌ Device removal failed: " . $e->getMessage() . PHP_EOL;
}
```

## 🔐 Authentication

The SMSGate client supports two authentication methods: Basic Authentication and JWT (JSON Web Token) authentication. Each method has its own use cases and benefits.

### Basic Authentication

```php
// Initialize client with Basic authentication
$client = new Client('your_login', 'your_password');
```

### JWT Authentication

JWT authentication uses bearer tokens for authentication.

#### Generating a JWT Token

```php
use AndroidSmsGateway\Client;
use AndroidSmsGateway\Domain\TokenRequest;

// First, create a client with Basic authentication to generate a token
$basicClient = new Client('your_login', 'your_password');

// Create a token request with specific scopes and TTL
$tokenRequest = new TokenRequest(
    ['messages:send', 'messages:read'],  // Scopes for permissions
    3600                                 // Token TTL in seconds (optional)
);

// Generate the token
$tokenResponse = $basicClient->GenerateToken($tokenRequest);
$jwtToken = $tokenResponse->AccessToken();

echo "Token generated! Expires at: " . $tokenResponse->ExpiresAt() . PHP_EOL;
```

#### Using a JWT Token

```php
// Initialize client with JWT authentication
$jwtClient = new Client(null, $jwtToken);

// Now use the client as usual
$message = (new MessageBuilder('Your message text here.', ['+1234567890']))->build();
$messageState = $jwtClient->SendMessage($message);
```

#### Revoking a JWT Token

```php
// Revoke a token using its ID (jti)
$basicClient->RevokeToken($tokenResponse->ID());
echo "Token revoked successfully!" . PHP_EOL;
```

## 📚 Full API Reference

### Client Initialization

The client supports two authentication methods: Basic Authentication and JWT Bearer Tokens.

#### Basic Authentication
```php
$clientBasic = new Client(
    string $login,
    string $password,
    string $serverUrl = 'https://api.sms-gate.app/3rdparty/v1',
    ?\Psr\Http\Client\ClientInterface $httpClient = null,
    ?\AndroidSmsGateway\Encryptor $encryptor = null
);

$clientJWT = new Client(
    null,                           // Set login to null for JWT
    string $jwtToken,               // JWT token as the second parameter
    string $serverUrl = 'https://api.sms-gate.app/3rdparty/v1',
    ?\Psr\Http\Client\ClientInterface $httpClient = null,
    ?\AndroidSmsGateway\Encryptor $encryptor = null
);
```

### Core Methods

| Category           | Method                                               | Description                       |
| ------------------ | ---------------------------------------------------- | --------------------------------- |
| **Messages**       | `SendMessage(Message $message)`                      | Send SMS message                  |
|                    | `GetMessageState(string $id)`                        | Get message status by ID          |
|                    | `RequestInboxExport(MessagesExportRequest $request)` | Request inbox export via webhooks |
| **Devices**        | `ListDevices()`                                      | List registered devices           |
|                    | `RemoveDevice(string $id)`                           | Remove device by ID               |
| **System**         | `HealthCheck()`                                      | Check API health status           |
|                    | `GetLogs(?string $from, ?string $to)`                | Retrieve system logs              |
| **Settings**       | `GetSettings()`                                      | Get account settings              |
|                    | `PatchSettings(Settings $settings)`                  | Partially update account settings |
|                    | `ReplaceSettings(Settings $settings)`                | Replace account settings          |
| **Webhooks**       | `ListWebhooks()`                                     | List registered webhooks          |
|                    | `RegisterWebhook(Webhook $webhook)`                  | Register new webhook              |
|                    | `DeleteWebhook(string $id)`                          | Delete webhook by ID              |
| **Authentication** | `GenerateToken(TokenRequest $request)`               | Generate a new JWT token          |
|                    | `RevokeToken(string $jti)`                           | Revoke a JWT token by ID          |

### Builder Methods
```php
// Message Builder
$message = (new MessageBuilder(string $text, array $recipients))
    ->setTtl(int $seconds)
    ->setSimNumber(int $simSlot)
    ->setWithDeliveryReport(bool $enable)
    ->setPriority(int $value)
    ->build();
```

## 🔒 Security Notes

### Best Practices

1. **Never store credentials in code** - Use environment variables:
   ```php
   $login = getenv('SMS_GATEWAY_LOGIN');
   $password = getenv('SMS_GATEWAY_PASSWORD');
   ```
2. **Use HTTPS** - Ensure all API traffic is encrypted
3. **Validate inputs** - Sanitize phone numbers and message content
4. **Rotate credentials** - Regularly update your API credentials

### Encryption Support

```php
use AndroidSmsGateway\Encryptor;

// Initialize client with encryption
$encryptor = new Encryptor('your-secret-passphrase');
$client = new Client($login, $password, Client::DEFAULT_URL, null, $encryptor);
```

## 👥 Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Development Setup
```bash
git clone https://github.com/android-sms-gateway/client-php.git
cd client-php
composer install
```

## 📄 License
This library is open-sourced software licensed under the [Apache-2.0 license](LICENSE).

---

**Note**: Android is a trademark of Google LLC. This project is not affiliated with or endorsed by Google.
