sdk-php
============

JSON-RPC 2.0 PHP class, to use with THECALLR API: http://thecallr.com/docs/

JSON-RPC 2.0 Specification: http://www.jsonrpc.org/specification

# Usage

*Init*
```php
$api = new \THECALLR\API\Client;
$api->setAuthCredentials('username', 'password');
```

*Simple method (no parameters)*
```php
$result = $api->call('system.get_timestamp');
```

*Method with parameters*
```php
$result = $api->call('apps.get_list', [false]);
```

More examples here: http://thecallr.com/docs/sdk/php/api/
*NOTE:* Using this library, you MUST pass the JSON-RPC parameters as an array (second parameter), like this:
```php
$result = $api->call('apps.create', ['REALTIME10', 'MY-RT-APP', null]);
```
and *NOT* like this (*deprecated*):
```php
$result = $api->call('apps.create', 'REALTIME10', 'MY-RT-APP', null);
```

The doc will be updated.