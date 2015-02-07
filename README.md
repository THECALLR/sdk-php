PHP SDK for THECALLR API
============

JSON-RPC 2.0 PHP class, to use with THECALLR API.

* **API documentation**: http://thecallr.com/docs/
* **Product page**: http://thecallr.com/en/products/api-json-voice-sms/
* **JSON-RPC 2.0 Specification**: http://www.jsonrpc.org/specification

* Easy to use Client class, built for PHP 5.4+
* Can be used for both the [API][docs-api] and [Realtime][docs-realtime]

## Composer

You should use Composer (https://getcomposer.org/) to manage your PHP dependencies.
If you do not have a `composer.json` file yet, create one at the root of your project, download Composer, and launch `composer.phar update`.

The `composer.json` file should look like this:
```json
{
  "require": {
    "thecallr/sdk-php": "dev-master"
  }
}
```

Add all the libaries you need in `composer.json`. Do not forget to run `composer.phar update` each time you edit the file.

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
```php
$result = $api->call('system.get_timestamp');
```

**Method with parameters**
```php
$result = $api->call('apps.get_list', [false]);
```

See examples in the "examples" directory.

[docs-api]: http://thecallr.com/docs/
[docs-realtime]: http://thecallr.com/docs/real-time/
