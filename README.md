# SMS Gateway for Android™ PHP API Client

This is a PHP client library for interfacing with the [SMS Gateway for Android](https://sms.capcom.me) API.

## Requirements

- PHP 7.4 or higher
- A PSR-18 compatible HTTP client implementation

## Installation

You can install the package via composer:

```bash
composer require capcom6/android-sms-gateway
```

## Usage

Here is a simple example of how to send a message using the library:


```php
<?php

require 'vendor/autoload.php';

use AndroidSmsGateway\Client;
use AndroidSmsGateway\Encryptor;
use AndroidSmsGateway\Domain\Message;

$login = 'your_login';
$password = 'your_password';

$client = new Client($login, $password);
// or
// $encryptor = new Encryptor('your_passphrase');
// $client = new Client($login, $password, Client::DEFAULT_URL, $httpClient, $encryptor);

$message = new Message('Your message text here.', ['+1234567890']);

try {
    $messageState = $client->Send($message);
    echo "Message sent with ID: " . $messageState->ID() . PHP_EOL;
} catch (Exception $e) {
    echo "Error sending message: " . $e->getMessage() . PHP_EOL;
    die(1);
}

try {
    $messageState = $client->GetState($messageState->ID());
    echo "Message state: " . $messageState->State() . PHP_EOL;
} catch (Exception $e) {
    echo "Error getting message state: " . $e->getMessage() . PHP_EOL;
    die(1);
}
```

## Client

The `Client` is used for sending SMS messages in plain text, but can also be used for sending encrypted messages by providing an `Encryptor`.

### Methods

The `Client` class has the following methods:

* `Send(Message $message)`: Send a new SMS message.
* `GetState(string $id)`: Retrieve the state of a previously sent message by its ID.

# Contributing

Contributions are welcome! Please submit a pull request or create an issue for anything you'd like to add or change.

# License

This library is open-sourced software licensed under the [Apache-2.0 license](LICENSE).
