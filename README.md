PHP SDK for CALLR API
============

JSON-RPC 2.0 PHP class, to use with CALLR API.

* **API documentation**: http://www.callr.com/docs/
* **SDK Installation guide**: see INSTALLING.php.md
* **Example projects**: https://github.com/THECALLR/examples-php
* **JSON-RPC 2.0 Specification**: http://www.jsonrpc.org/specification

* Easy to use Client class, built for **PHP 5.4+**
* Can be used for both the [API][docs-api] and [Real-time][docs-realtime] calls
* Requires: `php5-curl`

[docs-api]: http://www.callr.com/docs/
[docs-realtime]: https://www.callr.com/docs/voice/real-time/first-steps

Install with through composer with `composer req callr/sdk-php`

## Usage

### Init
```php
$api = new CALLR\API\Client;

// using login + password (note ; that is to be deprecated)
$api->setAuth(new CALLR\API\Authentication\LoginPasswordAuth('username', 'password'));

// If you are using a long-term token ("api-key"), here is what you need to do ;
$api->setAuth(new CALLR\API\Authentication\ApiKeyAuth('your-api-key'));
```

### Login-as
If you want to log in as another sub-customer or sub-user (one you have access
to), you can call the `logAs` method on the chosen authenticator :

```php
$auth = new CALLR\API\Authentication\LoginPasswordAuth('username', 'password');
$auth = $auth->logAs('User', 'username_2');

$api = new CALLR\API\Client;
$api->setAuth($auth);
```

Available authenticators are the classic login / password (sent through a BASIC
http request) or the Api-Key. Both supports the Login-As feature.

### Calling an api method
```php
//....
$result = $api->call('method', ['list', 'of', 'params']);
```
