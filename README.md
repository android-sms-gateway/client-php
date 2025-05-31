# SMS Gateway for Android™ PHP API Client

This is a PHP client library for interfacing with the [SMS Gateway for Android](https://sms-gate.app) API.

## Requirements

- PHP 7.4 or higher
- A PSR-18 compatible HTTP client implementation

## Installation

You can install the package via composer:

```bash
composer require capcom6/android-sms-gateway
```

## Usage

### Using the Builder Pattern

The library provides builder classes for creating `Message` and `Settings` objects with numerous optional fields.

#### Creating a Message

```php
<?php

require 'vendor/autoload.php';

use AndroidSmsGateway\Client;
use AndroidSmsGateway\Domain\MessageBuilder;

$login = 'your_login';
$password = 'your_password';

$client = new Client($login, $password);

$message = (new MessageBuilder('Your message text here.', ['+1234567890']))
    ->setTtl(3600)
    ->setSimNumber(1)
    ->setWithDeliveryReport(true)
    ->setPriority(100)
    ->build();

try {
    $messageState = $client->SendMessage($message);
    echo "Message sent with ID: " . $messageState->ID() . PHP_EOL;
} catch (Exception $e) {
    echo "Error sending message: " . $e->getMessage() . PHP_EOL;
    die(1);
}

try {
    $messageState = $client->GetMessageState($messageState->ID());
    echo "Message state: " . $messageState->State() . PHP_EOL;
} catch (Exception $e) {
    echo "Error getting message state: " . $e->getMessage() . PHP_EOL;
    die(1);
}
```

## Client

The `Client` is used for sending SMS messages in plain text, but can also be used for sending encrypted messages by providing an `Encryptor`.

### Message Methods

* `Send(Message $message)` (deprecated): Send a new SMS message.
* `SendMessage(Message $message)`: Send a new SMS message.
* `GetState(string $id)` (deprecated): Retrieve the state of a previously sent message by its ID.
* `GetMessageState(string $id)`: Retrieve the state of a previously sent message by its ID.

### Device Methods

* `ListDevices()`: List all registered devices.
* `RemoveDevice(string $id)`: Remove a device by ID.

### System Methods

* `HealthCheck()`: Check system health.
* `RequestInboxExport(object $request)`: Request inbox messages export.
* `GetLogs(?string $from = null, ?string $to = null)`: Get logs within a specified time range.

### Settings Methods

* `GetSettings()`: Get user settings.
* `UpdateSettings(object $settings)`: Update user settings.
* `PatchSettings(object $settings)`: Partially update user settings.

### Webhook Methods

* `ListWebhooks()`: List all registered webhooks.
* `RegisterWebhook(object $webhook)`: Register a new webhook.
* `DeleteWebhook(string $id)`: Delete a webhook by ID.

# Contributing

Contributions are welcome! Please submit a pull request or create an issue for anything you'd like to add or change.

# License

This library is open-sourced software licensed under the [Apache-2.0 license](LICENSE).
