# 📱 SMSGate PHP Client

[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stars][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![License][license-shield]][license-url]
[![Packagist Version][version-shield]][version-url]

A modern PHP client for the [SMSGate](https://sms-gate.app) API: send SMS messages and manage devices, webhooks, settings, and JWT tokens through your Android devices. PSR-18/PSR-17 compatible with any HTTP client via [php-http/discovery](https://github.com/php-http/discovery). See the [client libraries overview](https://docs.sms-gate.app/integration/client-libraries/) for the full ecosystem.

## 📖 About

`capcom6/android-sms-gateway` is a type-safe PHP library for the SMSGate 3rd-party API. It covers messages (send SMS and MMS, state, listing, cancellation), inbox refresh with individual or batch webhook delivery and attachment download, devices, webhooks, settings, logs, health checks, and the JWT token lifecycle, with a fluent `MessageBuilder` for message construction and an optional `Encryptor` for end-to-end encryption. Works with any PSR-18 HTTP client (Guzzle, curl, or others) and PHP 7.4+.

## 📚 Table of Contents

- [📱 SMSGate PHP Client](#-smsgate-php-client)
  - [📖 About](#-about)
  - [📚 Table of Contents](#-table-of-contents)
  - [⭐ Features](#-features)
  - [📦 Installation](#-installation)
  - [🔑 Authentication](#-authentication)
    - [Basic Authentication](#basic-authentication)
    - [JWT Authentication](#jwt-authentication)
  - [🚀 Quickstart](#-quickstart)
  - [💻 Usage](#-usage)
  - [⚙️ Configuration](#️-configuration)
  - [📖 API Reference](#-api-reference)
  - [🤝 Contributing](#-contributing)
  - [📄 License](#-license)

## ⭐ Features

- Fluent `MessageBuilder` for messages and `SettingsBuilder` for settings
- Messages: send SMS text or MMS (subject, body, attachments), state, listing, and cancellation
- PSR-18 HTTP client and PSR-17 factories, auto-discovered
- Basic and JWT authentication with token generation and revocation
- Inbox refresh with individual or batch webhook delivery and MMS attachment download
- Webhooks (single and batch events), devices, settings, logs, and health checks
- Optional end-to-end encryption via `Encryptor`
- Structured `HttpException` error handling

## 📦 Installation

```bash
composer require capcom6/android-sms-gateway
```

Requires PHP 7.4+ and a PSR-18 HTTP client implementation (e.g. Guzzle, `php-http/curl-client`).

## 🔑 Authentication

Two methods are supported: Basic authentication with account credentials, and JWT bearer tokens with scoped permissions. JWT is recommended for production.

### Basic Authentication

```php
// Basic authentication with account credentials
$client = new Client('your_login', 'your_password');
```

### JWT Authentication

```php
use AndroidSmsGateway\Domain\TokenRequest;

$basicClient = new Client('your_login', 'your_password');

$token = $basicClient->GenerateToken(
    new TokenRequest(['messages:send', 'messages:read'], 3600)
);

$jwtClient = new Client(null, $token->AccessToken());
```

## 🚀 Quickstart

```php
<?php

require 'vendor/autoload.php';

use AndroidSmsGateway\Client;
use AndroidSmsGateway\Domain\MessageBuilder;

$client = new Client('your_login', 'your_password');

$message = (new MessageBuilder('Hello from PHP', ['+15550100']))
    ->setWithDeliveryReport(true)
    ->build();

$state = $client->SendMessage($message);
echo 'Message ID: ' . $state->ID() . PHP_EOL;
```

## 💻 Usage

Beyond sending, the client covers message listing and cancellation, inbox listing and refresh, device management, health checks, logs, settings (get, patch, replace), webhooks, and token lifecycle. See [src/Client.php](https://github.com/android-sms-gateway/client-php/blob/master/src/Client.php) for the complete method list with signatures and [src/Domain](https://github.com/android-sms-gateway/client-php/tree/master/src/Domain) for the domain models.

## ⚙️ Configuration

The `Client` constructor accepts the following parameters:

| Parameter    | Required | Description                                                               |
| ------------ | -------- | ------------------------------------------------------------------------- |
| `$login`     | No       | Account login for Basic authentication                                    |
| `$password`  | Yes      | Account password (Basic) or a JWT token (Bearer)                          |
| `$serverUrl` | No       | API base URL; defaults to `https://api.sms-gate.app/3rdparty/v1`          |
| `$client`    | No       | PSR-18 HTTP client; auto-discovered via `php-http/discovery` when omitted |
| `$encryptor` | No       | Optional `Encryptor` for end-to-end encryption                            |

When `$login` is set, Basic authentication is used. When only `$password` is provided, it is sent as a Bearer JWT token.

## 📖 API Reference

- [Official API Reference](https://docs.sms-gate.app/integration/api/) - endpoints, payloads, and error codes
- [Authentication Guide](https://docs.sms-gate.app/integration/authentication/) - scopes and token management
- [Client libraries overview](https://docs.sms-gate.app/integration/client-libraries/)
- [Client source](https://github.com/android-sms-gateway/client-php/blob/master/src/Client.php) - full method reference and examples

## 🤝 Contributing

Contributions are welcome. Open an issue to discuss major changes before submitting a pull request; PRs target the `master` branch.

## 📄 License

Distributed under the Apache License 2.0. See [LICENSE](https://github.com/android-sms-gateway/client-php/blob/master/LICENSE).

<!-- Badge references: Shields.io style=for-the-badge is mandatory -->
[contributors-shield]: https://img.shields.io/github/contributors/android-sms-gateway/client-php?style=for-the-badge
[contributors-url]: https://github.com/android-sms-gateway/client-php/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/android-sms-gateway/client-php?style=for-the-badge
[forks-url]: https://github.com/android-sms-gateway/client-php/network/members
[stars-shield]: https://img.shields.io/github/stars/android-sms-gateway/client-php?style=for-the-badge
[stars-url]: https://github.com/android-sms-gateway/client-php/stargazers
[issues-shield]: https://img.shields.io/github/issues/android-sms-gateway/client-php?style=for-the-badge
[issues-url]: https://github.com/android-sms-gateway/client-php/issues
[license-shield]: https://img.shields.io/github/license/android-sms-gateway/client-php?style=for-the-badge
[license-url]: https://github.com/android-sms-gateway/client-php/blob/master/LICENSE
[version-shield]: https://img.shields.io/packagist/v/capcom6/android-sms-gateway?style=for-the-badge
[version-url]: https://packagist.org/packages/capcom6/android-sms-gateway
