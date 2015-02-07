PHP SDK for THECALLR API
============

JSON-RPC 2.0 PHP class, to use with THECALLR API.

* **API documentation**: http://thecallr.com/docs/
* **Product page**: http://thecallr.com/en/products/api-json-voice-sms/
* **JSON-RPC 2.0 Specification**: http://www.jsonrpc.org/specification

* Easy to use Client class, built for **PHP 5.4+**
* Can be used for both the [API][docs-api] and [Real-time][docs-realtime] calls
* Requires: `php5-curl`

## Composer

You should use Composer (https://getcomposer.org/) to manage your PHP dependencies.
If you do not have a `composer.json` file yet, create one at the root of your project, download Composer, and launch `composer update`.

The `composer.json` file should look like this:
```json
{
  "require": {
    "thecallr/sdk-php": "dev-master"
  }
}
```

Add all the libaries you need in `composer.json`. Do not forget to run `composer update` each time you edit the file.

Then you just need to include one file in your code:
```php
<?php

require 'vendor/autoload.php'
```

## Usage

**Init**
```php
$api = new \THECALLR\API\Client;
$api->setAuthCredentials('username', 'password');
```

**Simple method (no parameters)**
Get remote timestamp.
```php
$result = $api->call('system.get_timestamp');
```

**Method with parameters**
* List your Voice Apps, without showing assigned DIDs.
```php
$result = $api->call('apps.get_list', [false]);
```

* Send an SMS
```php
$result = $api->call('sms.send', ['THECALLR', '+33123456789', 'Hello, world!', null]);
```

* Start a Real-time outbound call
```php
$result = $api->call('dialr/call.realtime', ['DEADBEEF', ['number' => '+33123456789', 'timeout' => 30], null]);
```

See more examples in the "examples" directory.

[docs-api]: http://thecallr.com/docs/
[docs-realtime]: http://thecallr.com/docs/real-time/
